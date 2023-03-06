<?php
namespace backend\modules\rbac\models\form;

use Yii;
use backend\modules\rbac\models\User;
use yii\base\Model;

/**
 * Signup form
 */
class Signup extends Model
{
    public $username;
    public $email;
    public $password;
    public $status;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => 'backend\modules\rbac\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => 'backend\modules\rbac\models\User', 'message' => 'This email address has already been taken.'],
            [['status'], 'integer'],
            //[['status'], 'default', 'value' => \backend\modules\rbac\models\User::STATUS_INACTIVE],
            ['password', 'required'],
            ['password', 'string', 'min' => 10],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {

        if ($this->validate()) {
            $user = new User();
            $user->username = $this->username;
            $user->email = $this->email;
            $user->status = $this->status;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            if ($user->save()) {
                //$this->sentUserNotification();
                return $user;
            }
        }

        return null;
    }

    public function sentUserNotification()
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'user-register-html', 'text' => 'user-register-text'],
                ['model' => $this]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Registration ' . Yii::$app->name)
            ->send();
    }
}
