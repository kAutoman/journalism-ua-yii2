<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\db\Query;
use yii\helpers\Console;
use console\models\User;

/**
 * Class UserController
 *
 * @package console\controllers
 */
class UserController extends Controller
{
    /**
     * @var string
     */
    public $defaultAction = 'check';

    /**
     * @var string
     */
    private $_devEmail = 'admin@dev.dev';


    /**
     * @param int $timeout
     */
    public function actionCheck($timeout = 86400)
    {
        $time = time() - $timeout;

        /** @var Query $query */
        $query = User::find();

        $query->andWhere(['status' => User::STATUS_SUSPENDED]);
        $query->andWhere(['<=', 'block_at', $time]);

        /** @var User[] $models */
        $models = $query->all();

        foreach ($models as $model) {
            if ($model->updateAttributes(['status' => User::STATUS_ACTIVE, 'block_at' => null])) {
                $this->stdout("User {$model->email} successfully unlocked.\n", Console::FG_GREEN);
            } else {
                $this->stdout("Error unblocking user {$model->email}.\n", Console::FG_RED);
            }
        }
    }

    /**
     * @param $email
     */
    public function actionBlock($email)
    {
        $user = User::findOne(['email' => $email]);

        if ($user) {
            if ($user->updateAttributes(['status' => User::STATUS_SUSPENDED, 'block_at' => time()])) {
                $this->stdout("User successfully locked.\n", Console::FG_GREEN);
            } else {
                $this->stdout("Error blocking user.\n", Console::FG_RED);
            }
        } else {
            $this->stdout("User not found.\n", Console::FG_RED);
        }
    }

    /**
     * @param $email
     */
    public function actionUnBlock($email)
    {
        $user = User::findOne(['email' => $email]);

        if ($user) {
            if ($user->updateAttributes(['status' => User::STATUS_ACTIVE, 'block_at' => null])) {
                $this->stdout("User successfully unlocked.\n", Console::FG_GREEN);
            } else {
                $this->stdout("Error unblocking user.\n", Console::FG_RED);
            }
        } else {
            $this->stdout("User not found.\n", Console::FG_RED);
        }
    }

    /**
     * Generates new auth data for Dev user
     *
     * @param string $password New password
     * If set `null` - password will be generated
     * @return int
     */
    public function actionChangeMainAdminPassword($password = null)
    {
        $security = Yii::$app->getSecurity();

        if ($password === null) {
            $password = $security->generateRandomString(10);
        }

        /* @var User $dev */
        $dev = User::findOne(['email' => $this->_devEmail]);
        $dev->auth_key = $security->generateRandomString();
        $dev->password_hash = $security->generatePasswordHash($password);
        $dev->password_reset_token = $security->generateRandomString() . '_' . time();

        if ($dev->update()) {
            $this->stdout("New password: $password\n", Console::FG_GREEN);
            return self::EXIT_CODE_NORMAL;
        }

        $this->stderr("Updating of auth data was failed!\n", Console::FG_RED);
        return self::EXIT_CODE_ERROR;
    }

    /**
     * Creation of user account for client
     *
     * @param string $email Client e-mail
     * @param string $password Password for client account
     * @param string $username Client username
     * @param string $role
     * @return int
     * @throws \Exception
     */
    public function actionCreateNew($email, $password, $username = null, $role = null)
    {
        $security = Yii::$app->getSecurity();
        $username = ($username === null) ? $email : $username;

        $user = new User([
            'username' => $username,
            'auth_key' => $security->generateRandomString(),
            'password_hash' => $security->generatePasswordHash($password),
            'password_reset_token' => $security->generateRandomString() . '_' . time(),
            'email' => $email,
            'status' => User::STATUS_ACTIVE,
        ]);

        $transaction = User::getDb()->beginTransaction();
        try {
            if ($user->save(false)) {
                $message = 'User was successfully created!';
                $message .= "\nE-mail: $email";
                $message .= "\nUsername: $username";
                $message .= "\nPassword: $password\n";

                if ($role !== null) {
                    $auth = Yii::$app->getAuthManager();

                    if (!$auth->getAssignment($role, $user->id)) {
                        if ($roleToAssign = $auth->getRole($role)) {
                            $auth->assign($roleToAssign, $user->id);
                            $message .= "Assigned role: $role\n";
                        } else {
                            $transaction->rollBack();
                            $this->stderr("Role with name `$role` not found!\n", Console::FG_RED);
                            return self::EXIT_CODE_ERROR;
                        }
                    }
                }
                $transaction->commit();

                $this->stdout($message, Console::FG_GREEN);
                return self::EXIT_CODE_NORMAL;
            }
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }

        $this->stderr("Creation of user was failed!\n", Console::FG_RED);
        return self::EXIT_CODE_ERROR;
    }
}
