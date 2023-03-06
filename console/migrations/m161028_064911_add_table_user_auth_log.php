<?php

namespace console\migrations;

use console\components\Migration;

/**
 * Class m161028_064911_add_table_user_auth_log migration
 */
class m161028_064911_add_table_user_auth_log extends Migration
{
    /**
     * migration table name
     */
    public $tableName = '{{%user_auth_log}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'userId' => $this->integer(),
                'date' => $this->integer(),
                'cookieBased' => $this->boolean(),
                'duration' => $this->integer(),
                'error' => $this->string(),
                'ip' => $this->string(),
                'host' => $this->string(),
                'url' => $this->string(),
                'userAgent' => $this->string(),
            ],
            $this->tableOptions
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
