<?php

namespace console\migrations;

use console\components\Migration;

/**
 * Class m161102_124141_add_table_ip_block migration
 */
class m161102_124141_add_table_ip_block extends Migration
{
    /**
     * migration table name
     */
    public $tableName = '{{%ip_block}}';

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
