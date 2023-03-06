<?php

namespace console\migrations;

use console\components\Migration;

class m200310_083354_alter_table_competition_request extends Migration
{
    public $table = '{{%competition_request}}';

    public function safeUp()
    {
        $this->addColumn($this->table, 'status', $this->integer()->defaultValue(0));
    }

    public function safeDown()
    {
        $this->dropColumn($this->table, 'status');
    }
}
