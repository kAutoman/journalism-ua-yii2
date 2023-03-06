<?php

namespace console\migrations;

use console\components\Migration;
use Yii;
use yii\rbac\DbManager;

/**
 * Class m150212_155252_insert_admin_role migration
 */
class m150212_155252_insert_admin_role extends Migration
{
    /**
     * @throws \yii\base\InvalidConfigException
     * @return DbManager
     */
    protected function getAuthManager()
    {
        $authManager = Yii::$app->getAuthManager();
        if (!$authManager instanceof DbManager) {
            throw new \yii\base\InvalidConfigException('You should configure "authManager" component to use database before executing this migration.');
        }
        return $authManager;
    }

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $authManager = $this->getAuthManager();
        $this->db = $authManager->db;

        $this->insert($authManager->itemTable, [
            'name' => \common\models\User::ROLE_ADMIN,
            'type' => \yii\rbac\Item::TYPE_ROLE,
            'description' => 'admin role',
            'rule_name' => null,
            'data' => null,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $authManager = $this->getAuthManager();
        $this->db = $authManager->db;

        $this->delete($authManager->itemTable, ['name' => \common\models\User::ROLE_ADMIN]);
    }
}
