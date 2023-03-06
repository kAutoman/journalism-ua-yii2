<?php

namespace backend\modules\rbac\models\form;

use backend\modules\rbac\models\User;
use Throwable;
use Yii;
use yii\base\Model;

/**
 * Description of ChangePassword
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class ChangePassword extends Model
{
    public $oldPassword;
    public $newPassword;
    public $retypePassword;
    public $do_logout;
    public $id;

    public function doLogoutList(): array
    {
        return [
            0 => Yii::t('yii', 'No'),
            1 => Yii::t('yii', 'Yes'),
        ];
    }

    public function rules()
    {
        return [
            [['oldPassword', 'newPassword', 'retypePassword'], 'required'],
            [['oldPassword'], 'validatePassword'],
            [['newPassword'], 'string', 'min' => 6],
            [['do_logout'], 'boolean'],
            [['do_logout'], 'default', 'value' => false],
            [['retypePassword'], 'compare', 'compareAttribute' => 'newPassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     */
    public function validatePassword()
    {
        /* @var $user User */
        $user = User::findOne(['id' => $this->id]);
        if (!$user || !$user->validatePassword($this->oldPassword)) {
            $this->addError('oldPassword', 'Incorrect old password.');
        }
    }

    /**
     * Change password.
     * @return User|null the saved model or null if saving fails
     */
    public function change()
    {
        if ($this->validate()) {
            $needLogout = (bool)$this->do_logout;
            /* @var $user User */
            $user = User::findOne(['id' => $this->id]);
            $user->setPassword($this->newPassword);
            $user->generateAuthKey();
            if ($user->save()) {
                if ($needLogout) {
                    $this->endAllUserSessions($user);
                }

                return true;
            }
        }

        return false;
    }

    private function endAllUserSessions(\common\models\User $user)
    {
        try {
            $sessID = app()->session->getId();
            db()->createCommand()->delete(app()->session->sessionTable, [
                'and', ['user_id' => $user->id], ['!=', 'id', $sessID]
            ])->execute();

        } catch (Throwable $e) {
            logError($e->getMessage());
        }
    }
}
