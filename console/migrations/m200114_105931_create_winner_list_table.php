<?php

namespace console\migrations;

use console\components\Migration;

/**
 * Class m200114_105931_create_winner_list_table migration
 */
class m200114_105931_create_winner_list_table extends Migration
{
    /**
     * migration table name
     */
    public $tableName = '{{%winner_list}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'member_item_id' => $this->integer()->notNull()->comment('Member'),
                'publication_link' => $this->string()->null()->comment('Publication link'),
                'published' => $this->boolean()->notNull()->defaultValue(1)->comment('Published'),
                'position' => $this->integer()->notNull()->defaultValue(0)->comment('Position'),
                'created_at' => $this->integer()->notNull()->comment('Created At'),
                'updated_at' => $this->integer()->notNull()->comment('Updated At'),
            ],
            $this->tableOptions
        );
        $this->addForeignKey(
            'fk-winner_list-member_item_id-member_item-id',
            $this->tableName,
            'member_item_id',
            '{{%member_item}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-winner_list-member_item_id-member_item-id', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
