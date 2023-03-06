<?php

namespace console\migrations;

use console\components\Migration;

/**
 * Class m200114_073715_create_home_partner_item_table migration
 */
class m200114_073715_create_home_partner_item_table extends Migration
{
    /**
     * migration table name
     */
    public $tableName = '{{%home_partner_item}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'link' => $this->string()->notNull()->comment('Link'),
                'published' => $this->boolean()->notNull()->defaultValue(1)->comment('Published'),
                'position' => $this->integer()->notNull()->defaultValue(0)->comment('Position'),
                'created_at' => $this->integer()->notNull()->comment('Created At'),
                'updated_at' => $this->integer()->notNull()->comment('Updated At'),
            ],
            $this->tableOptions
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
