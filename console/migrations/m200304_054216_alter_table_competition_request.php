<?php

namespace console\migrations;

use console\components\Migration;

class m200304_054216_alter_table_competition_request extends Migration
{
    public $table = '{{%competition_request}}';

    public function safeUp()
    {
        $this->addColumn($this->table, 'gender_id', $this->integer()->null());
        $this->addColumn($this->table, 'material_type_id', $this->integer()->null());
        $this->addColumn($this->table, 'nomination_id', $this->integer()->null());
    }

    public function safeDown()
    {
        $this->dropColumn($this->table, 'gender_id');
        $this->dropColumn($this->table, 'material_type_id');
        $this->dropColumn($this->table, 'nomination_id');
    }
}
