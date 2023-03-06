<?php

namespace console\migrations;

use common\models\User;
use console\components\Migration;
use Yii;
use yii\base\ExitException;
use yii\base\InvalidConfigException;
use yii\helpers\Console;
use yii\rbac\DbManager;

/**
 * Class m150212_160541_insert_admin_assignment migration
 */
class m150212_160541_insert_admin_assignment extends Migration
{
    private $userId;

    public function init()
    {
        parent::init();

        $this->userId = $this->getUserId();
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @return DbManager
     */
    protected function getAuthManager()
    {
        $authManager = Yii::$app->getAuthManager();
        if (!$authManager instanceof DbManager) {
            throw new InvalidConfigException('You should configure "authManager" component to use database before executing this migration.');
        }

        return $authManager;
    }

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $authManager = $this->getAuthManager();
        $role = $authManager->getRole(User::ROLE_ADMIN);
        $authManager->assign($role, $this->userId);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $authManager = $this->getAuthManager();
        $role = $authManager->getRole(User::ROLE_ADMIN);
        $authManager->revoke($role, $this->userId);
    }

    protected function getUserId(): int
    {
        $query = (new \yii\db\Query())
            ->select(['id'])
            ->from(['user' => User::tableName()])
            ->where(['email' => 'admin@dev.dev'])
            ->one();

        if ($query === false) {
            $msg = Console::ansiFormat("\n    Migration failed. User not found.\n\n", [
                Console::FG_YELLOW,
                Console::BG_RED,
                Console::BOLD,
            ]);

            throw new ExitException($msg);
        }

        return $query['id'];
    }
}
