<?php

namespace console\migrations;

use console\components\Migration;

/**
 * Class m190912_114552_create_category_news_table_lang migration
 */
class m190912_114552_create_category_news_table_lang extends Migration
{
    /**
     * Migration related table name
     */
    public $tableName = '{{%category_news_lang}}';

    /**
     * main table name, to make constraints
     */
    public $tableNameRelated = '{{%category_news}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(
            $this->tableName,
            [
                'model_id' => $this->integer()->notNull()->comment('Related model id'),
                'language' => $this->string(16)->notNull()->comment('Language'),

                'label' => $this->string()->notNull()->comment('Label'),
                'content' => $this->text()->null()->comment('Content'),
            ],
            $this->tableOptions
        );

        
        $this->addPrimaryKey('pk-category_news_lang', $this->tableName, ['model_id', 'language']);

        $this->addForeignKey(
            'fk-category_news_lang-model_id-category_news-id',
            $this->tableName,
            'model_id',
            $this->tableNameRelated,
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
        $this->dropTable($this->tableName);
    }
}

