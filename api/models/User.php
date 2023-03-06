<?php


namespace api\models;


use yii\web\UnauthorizedHttpException;

class User extends \common\models\User
{
    public function fields()
    {
        return [
            'token' => function (self $model) {
                return $model->auth_key;
            },
            'reset_token' => function (self $model) {
                return $model->password_reset_token;
            },
            'expire_at'
        ];
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        $user = static::find()->where(['auth_key' => $token, 'status' => self::STATUS_ACTIVE])->one();
        if (!$user) {
            return false;
        }
        if ($user->expire_at < time()) {
            throw new UnauthorizedHttpException('the access - token expired ', -1);
        } else {
            return $user;
        }
    }
}
