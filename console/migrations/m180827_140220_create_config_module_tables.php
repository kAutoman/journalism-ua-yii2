<?php

namespace console\migrations;

use console\components\Migration;

class m180827_140220_create_config_module_tables extends Migration
{
    public $table = '{{%config}}';

    public function safeUp()
    {
        $this->createTable($this->table, [
            'key' => $this->string(191)->notNull(),
            'lang' => $this->string(7)->notNull(),
            'value' => $this->text()->null(),
        ], $this->tableOptions);

        $this->addPrimaryKey('pk-config', $this->table, ['key', 'lang']);
    }

    public function safeDown()
    {
        $this->dropTable($this->table);
    }
}
