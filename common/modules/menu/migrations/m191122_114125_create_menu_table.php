<?php

namespace common\modules\menu\migrations;

use console\components\Migration;

/**
 * Class m191122_114125_create_menu_table migration
 */
class m191122_114125_create_menu_table extends Migration
{
    /**
     * migration table name
     */
    public $tableName = '{{%menu}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'location' => $this->smallInteger()->notNull()->defaultValue(0)->comment('Location'),
                'link' => $this->string()->notNull()->comment('Link'),
                'published' => $this->boolean()->notNull()->defaultValue(true)->comment('Published'),
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
