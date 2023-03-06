<?php

namespace console\migrations;

use console\components\Migration;

/**
 * Class m190912_115841_create_news_to_category_table migration
 */
class m190912_115841_create_news_to_category_table extends Migration
{
    /**
     * migration table name
     */
    public $tableName = '{{%news_to_category}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'category_id' => $this->integer()->notNull()->comment('category'),
                'news_id' => $this->integer()->notNull()->comment('news'),
            ],
            $this->tableOptions
        );
        $this->addForeignKey(
            'fk-news_to_category-category_id-category_news-id',
            $this->tableName,
            'category_id',
            '{{%category_news}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-news_to_category-news_id-news-id',
            $this->tableName,
            'news_id',
            '{{%news}}',
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
        $this->dropForeignKey('fk-news_to_category-category_id-category_news-id', $this->tableName);
        $this->dropForeignKey('fk-news_to_category-news_id-news-id', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
