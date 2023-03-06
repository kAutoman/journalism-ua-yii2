<?php

namespace console\migrations;

use common\models\User;
use Yii;
use console\components\Migration;
use yii\base\InvalidConfigException;
use yii\rbac\DbManager;

class m200310_063648_create_jury_users extends Migration
{
    public $tableName = '{{%user}}';

    /**
     * @return \yii\rbac\ManagerInterface
     * @throws InvalidConfigException
     */
    protected function getAuthManager()
    {
        $authManager = Yii::$app->getAuthManager();
        if (!$authManager instanceof DbManager) {
            throw new InvalidConfigException('You should configure "authManager" component to use database before executing this migration.');
        }

        return $authManager;
    }

    public function safeUp()
    {
        $authManager = $this->getAuthManager();

        $this->insert(
            $this->tableName,
            [
                'username' => 'jury_admin',
                'auth_key' => Yii::$app->security->generateRandomString(),
                'password_hash' => Yii::$app->security->generatePasswordHash('jury_admin'),
                'password_reset_token' => Yii::$app->security->generateRandomString() . '_' . time(),
                'email' => 'jury_admin@dev.dev',
                'status' => \common\models\User::STATUS_ACTIVE,
                'created_at' => time(),
                'updated_at' => time(),
            ]
        );

        $this->insert(
            $this->tableName,
            [
                'username' => 'jury',
                'auth_key' => Yii::$app->security->generateRandomString(),
                'password_hash' => Yii::$app->security->generatePasswordHash('jury'),
                'password_reset_token' => Yii::$app->security->generateRandomString() . '_' . time(),
                'email' => 'jury@dev.dev',
                'status' => \common\models\User::STATUS_ACTIVE,
                'created_at' => time(),
                'updated_at' => time(),
            ]
        );

        $this->insert(
            $this->tableName,
            [
                'username' => 'moderator',
                'auth_key' => Yii::$app->security->generateRandomString(),
                'password_hash' => Yii::$app->security->generatePasswordHash('moderator'),
                'password_reset_token' => Yii::$app->security->generateRandomString() . '_' . time(),
                'email' => 'moderator@dev.dev',
                'status' => \common\models\User::STATUS_ACTIVE,
                'created_at' => time(),
                'updated_at' => time(),
            ]
        );

        $juryAdmin = User::findOne(['username' => 'jury_admin']);

        if ($juryAdmin) {
            $roleJuryAdmin = $authManager->getRole(User::ROLE_JURY_ADMIN);

            $authManager->assign($roleJuryAdmin, $juryAdmin->getId());
        }

        $jury = User::findOne(['username' => 'jury']);

        if ($jury) {
            $roleJury = $authManager->getRole(User::ROLE_JURY);

            $authManager->assign($roleJury, $jury->getId());
        }

        $moderator = User::findOne(['username' => 'moderator']);

        if ($moderator) {
            $roleModerator = $authManager->getRole(User::ROLE_MODERATOR);

            $authManager->assign($roleModerator, $moderator->getId());
        }
    }

    public function safeDown()
    {
        $juryAdmin = User::findOne(['username' => 'jury_admin']);

        if ($juryAdmin) {
            $juryAdmin->delete();
        }

        $jury = User::findOne(['username' => 'jury']);

        if ($jury) {
            $jury->delete();
        }

        $moderator = User::findOne(['username' => 'moderator']);

        if ($moderator) {
            $moderator->delete();
        }
    }
}
