<?php

namespace console\migrations;

use console\components\Migration;

class m200303_095446_alter_table_competition_request extends Migration
{
    public $table = '{{%competition_request}}';

    public function safeUp()
    {
        $this->alterColumn($this->table, 'material_label', $this->text()->null());
        $this->alterColumn($this->table, 'program_link', $this->text()->null());
        $this->alterColumn($this->table, 'nomination', $this->text()->null());
        $this->alterColumn($this->table, 'argument', $this->text()->null());
        $this->alterColumn($this->table, 'awards', $this->text()->null());
    }

    public function safeDown()
    {
        $this->alterColumn($this->table, 'material_label', $this->string()->null());
        $this->alterColumn($this->table, 'program_link', $this->string()->null());
        $this->alterColumn($this->table, 'nomination', $this->string()->null());
        $this->alterColumn($this->table, 'argument', $this->string()->null());
        $this->alterColumn($this->table, 'awards', $this->string()->null());
    }
}
