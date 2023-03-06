<?php


namespace api\models;


use Yii;
use yii\base\Model;

class Refresh extends Model
{
    /**@var $_user User */
    private $_user;

    public $reset_token;

    public function rules()
    {
        return [
            [['reset_token'], 'required'],
            [['reset_token'], 'string', 'max' => 255],
            [['reset_token'], 'validateReset']
        ];
    }

    public function validateReset($attribute)
    {
        if($this->getUser()){

            if($this->_user::isPasswordResetTokenValid($this->$attribute)){
                $this->addError($attribute, 'Auth token is not exprired');
            }
        } else {
            $this->addError($attribute, 'User with this token not found');
        }
    }


    public function reset()
    {
        if ($this->validate()) {

            if ($this->getUser()) {
                if (!$this->_user::isPasswordResetTokenValid($this->reset_token)) {
                    $this->_user->generateAuthKey();
                    $this->_user->generatePasswordResetToken();
                    $this->_user->expire_at = time() + (Yii::$app->params['user.passwordResetTokenExpire'] ?? 3600);
                    $this->_user->save();
                }
                Yii::$app->user->login($this->_user, Yii::$app->params['user.passwordResetTokenExpire'] ?? 3600);
                return $this->_user;
            }
        }
        return false;
    }

    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByPasswordResetToken($this->reset_token);
        }

        return $this->_user;
    }
}
