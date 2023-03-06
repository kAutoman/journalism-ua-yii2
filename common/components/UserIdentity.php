<?php
namespace common\components;

use common\models\User;
use Yii;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;

/**
 * UserIdentity
 */
class UserIdentity implements IdentityInterface
{
    /** @var User */
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        /** @var User $user */
        $user = User::findOne(['id' => $id, 'status' => User::STATUS_ACTIVE]);
        if (!$user) {
            return null;
        }
        return new static($user);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->user->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->user->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->user->password_hash);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return UserIdentity|null
     */
    public static function findByEmail($username)
    {
        /** @var User $user */
        $user = User::findOne(['email' => $username, 'status' => User::STATUS_ACTIVE]);
        if (!$user) {
            return null;
        }
        return new static($user);
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->user->username;
    }
}
