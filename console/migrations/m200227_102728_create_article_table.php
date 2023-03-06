<?php

namespace console\migrations;

use console\components\Migration;

/**
 * Class m200227_102728_create_article_table migration
 */
class m200227_102728_create_article_table extends Migration
{
    /**
     * migration table name
     */
    public $tableName = '{{%article}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'alias' => $this->string()->notNull()->comment('Alias'),
                'publication_date' => $this->integer()->notNull()->comment('Published date'),
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
