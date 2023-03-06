<?php

namespace console\migrations;

use console\components\Migration;

/**
 * Class m161031_101448_mod_table_user migration
 */
class m161031_101448_mod_table_user extends Migration
{
    /**
     * migration table name
     */
    public $tableName = '{{%user}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'block_at', $this->integer()->defaultValue(null));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'block_at');
    }
}
