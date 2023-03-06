<?php


namespace api\models;


use api\models\User;
use Yii;
use yii\base\Model;

class Login extends Model
{
    /**@var $_user User* */
    private $_user;
    public $login;
    public $password;

    public function rules()
    {
        return [
            [
                ['login', 'password'],
                'required'
            ],
            ['password', 'validatePassword'],
        ];

    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, bt('Incorrect username or password.', 'api'));
            }
        }
    }

    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByEmail($this->login);
        }

        return $this->_user;
    }


    public function login()
    {
        if ($this->validate()) {

            if ($this->getUser()) {
                if ($this->_user->expire_at < time()) {

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
}
