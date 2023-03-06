<?php

namespace console\migrations;

use console\components\Migration;

class m200313_090913_create_table_user_to_member_item extends Migration
{
    public $tableName = '{{%user_to_member_item}}';

    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'user_id' => $this->integer()->notNull(),
            'member_item_id' => $this->integer()->notNull(),
        ], $this->tableOptions);

        $this->addForeignKey(
            'fk-user_to_member_item-user_id-user-id',
            $this->tableName,
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-user_to_member_item-member_item_id-member_item-id',
            $this->tableName,
            'member_item_id',
            '{{%member_item}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-user_to_member_item-member_item_id-member_item-id', $this->tableName);
        $this->dropForeignKey('fk-user_to_member_item-user_id-user-id', $this->tableName);

        $this->dropTable($this->tableName);
    }
}
