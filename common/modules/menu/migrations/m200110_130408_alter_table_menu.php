<?php

namespace common\modules\menu\migrations;

use console\components\Migration;

class m200110_130408_alter_table_menu extends Migration
{
    public $table = '{{%menu}}';

    public function safeUp()
    {
        $this->addColumn($this->table, 'module', $this->integer()->null());
    }

    public function safeDown()
    {
        $this->dropColumn($this->table, 'module');
    }
}
