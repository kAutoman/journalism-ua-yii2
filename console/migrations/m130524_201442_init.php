<?php

namespace console\migrations;

use console\components\Migration;

class m130524_201442_init extends Migration
{
    /**
     * migration table name
     */
    public $tableName = '{{%user}}';

    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->comment('User name'),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string(),
            'email' => $this->string()->notNull()->comment('Email'),
            'status' => $this->smallInteger()->notNull()->defaultValue(\common\models\User::STATUS_ACTIVE)->comment('Status, model constant value (Active, Deleted, etc)'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $this->tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
