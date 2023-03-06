<?php

namespace console\migrations;

use console\components\Migration;

/**
 * Class m200111_073945_create_social_table migration
 */
class m200111_073945_create_social_table extends Migration
{
    /**
     * migration table name
     */
    public $tableName = '{{%social}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'link' => $this->string()->notNull()->comment('Label'),
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
