<?php

namespace console\migrations;

use console\components\Migration;

/**
 * Class m190912_114552_create_category_news_table migration
 */
class m190912_114552_create_category_news_table extends Migration
{
    /**
     * migration table name
     */
    public $tableName = '{{%category_news}}';

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
                'published' => $this->boolean()->notNull()->defaultValue(true)->comment('Published'),
                'position' => $this->integer()->notNull()->defaultValue(0)->comment('Position'),
                'created_at' => $this->integer()->notNull()->comment('Created At'),
                'updated_at' => $this->integer()->notNull()->comment('Updated At'),
            ],
            $this->tableOptions
        );
        $this->createIndex('idx-category_news-alias', 'category_news', 'alias', true);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
