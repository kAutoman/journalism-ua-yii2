<?php

namespace console\migrations;

use console\components\Migration;

/**
 * Class m161102_105557_add_table_ip_auth_log migration
 */
class m161102_105557_add_table_ip_auth_log extends Migration
{
    /**
     * migration table name
     */
    public $tableName = '{{%ip_auth_log}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'date' => $this->integer()->notNull()->comment('Date'),
                'error' => $this->string()->defaultValue(null)->comment('Error'),
                'ip' => $this->string()->notNull()->comment('Ip'),
                'host' => $this->string()->notNull()->comment('Host'),
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
