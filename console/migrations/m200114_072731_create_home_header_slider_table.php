<?php

namespace console\migrations;

use console\components\Migration;

/**
 * Class m200114_072731_create_home_header_slider_table migration
 */
class m200114_072731_create_home_header_slider_table extends Migration
{
    /**
     * migration table name
     */
    public $tableName = '{{%home_header_slider}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'button_form_enable' => $this->boolean()->null()->comment('Button form enable'),
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
