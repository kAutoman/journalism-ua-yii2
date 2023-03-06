<?php
namespace console\migrations;

use console\components\Migration;

class m190830_075758_add_position_to_table extends Migration
{
    public $table = '{{%fpm_file}}';

    public function safeUp()
    {
        $this->addColumn($this->table, 'position', $this->integer()->null());
    }

    public function safeDown()
    {
        $this->dropColumn($this->table, 'position');
    }
}
