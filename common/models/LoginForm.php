<?php
namespace common\models;

use common\components\IpLogLoginFormBehavior;
use common\models\User;
use Yii;
use yii\base\Model;
use yii\web\IdentityInterface;
use yii2tech\authlog\AuthLogLoginFormBehavior;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $email;

    public $password;

    public $rememberMe = true;

    public $verifyCode;

    private $_user = false;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            ['email', 'email'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
            ['verifyCode', 'safe'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect email or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByEmail($this->email);
        }

        return $this->_user;
    }

    public function behaviors()
    {
        return [
            'authLog' => [
                'class' => AuthLogLoginFormBehavior::className(),
                'findIdentity' => 'getUser',
                'verifyRobotAttribute' => 'verifyCode',
                'deactivateIdentity' => function (IdentityInterface $identity) {
                    /** @var User $identity */
                    //return $identity->updateAttributes(['status' => User::STATUS_SUSPENDED, 'block_at' => time()]);
                },
            ],
            'ipLog' => [
                'class' => IpLogLoginFormBehavior::className(),
                'findIdentity' => 'getUser',
            ],
        ];
    }
}
