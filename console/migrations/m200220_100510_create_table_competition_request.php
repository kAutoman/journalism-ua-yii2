<?php

namespace console\migrations;

use console\components\Migration;

class m200220_100510_create_table_competition_request extends Migration
{
    public $table = '{{%competition_request}}';

    public function safeUp()
    {
        $this->createTable($this->table, [
            'id' => $this->primaryKey(),

            'name' => $this->string()->null(),
            'email' => $this->string()->null(),
            'gender' => $this->string()->null(),
            'age' => $this->string()->null(),
            'city' => $this->string()->null(),
            'company_name' => $this->string()->null(),
            'position' => $this->string()->null(),
            'phone' => $this->string()->null(),
            'experience' => $this->string()->null(),

            'other_name' => $this->string()->null(),
            'material_label' => $this->string()->null(),
            'material_type' => $this->string()->null(),
            'program_label' => $this->string()->null(),
            'program_published_date' => $this->string()->null(),
            'program_link' => $this->string()->null(),
            'nomination' => $this->string()->null(),
            'argument' => $this->string()->null(),
            'awards' => $this->string()->null(),

            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $this->tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable($this->table);
    }
}
