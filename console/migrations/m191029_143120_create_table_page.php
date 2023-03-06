<?php

namespace console\migrations;

use console\components\Migration;

class m191029_143120_create_table_page extends Migration
{
    public $table = '{{%page}}';

    public function safeUp()
    {
        $this->createTable($this->table, [
            'id' => $this->primaryKey(),
            'type' => $this->integer()->null()->defaultValue(0),
            // structure organize
            'root' => $this->integer(),
            'lft' => $this->integer()->notNull(),
            'rgt' => $this->integer()->notNull(),
            'depth' => $this->integer()->notNull(),
            'entity_id' => $this->string()->notNull()->unique(),
            // content part
//            'title' => $this->string()->notNull(),
            'alias' => $this->string()->notNull()->unique(),
//            'content' => $this->text()->null(),
            // states
            'published' => $this->boolean()->notNull()->defaultValue(true),
            'movable_u' => $this->boolean()->notNull()->defaultValue(true),
            'movable_d' => $this->boolean()->notNull()->defaultValue(true),
            'movable_l' => $this->boolean()->notNull()->defaultValue(true),
            'movable_r' => $this->boolean()->notNull()->defaultValue(true),
            'removable' => $this->boolean()->notNull()->defaultValue(true),
            'child_allowed' => $this->boolean()->notNull()->defaultValue(true),
            // optimistic lock
            'deleted' => $this->boolean()->notNull()->defaultValue(false),
            'lock' => $this->bigInteger()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull()->defaultValue(0),
            'updated_at' => $this->integer()->notNull()->defaultValue(0),
            'deleted_at' => $this->integer()->null()->defaultValue(null),
        ]);

        $this->createIndex('lft', $this->table, ['lft', 'rgt']);
        $this->createIndex('rgt', $this->table, ['rgt']);
        $this->createIndex('published', $this->table, ['published']);
        $this->createIndex('deleted', $this->table, ['deleted']);
    }

    public function safeDown()
    {
        $this->dropTable($this->table);
    }
}
