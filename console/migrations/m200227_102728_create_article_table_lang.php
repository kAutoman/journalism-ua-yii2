<?php

namespace console\migrations;

use console\components\Migration;

/**
 * Class m200227_102728_create_article_table_lang migration
 */
class m200227_102728_create_article_table_lang extends Migration
{
    /**
     * Migration related table name
     */
    public $tableName = '{{%article_lang}}';

    /**
     * main table name, to make constraints
     */
    public $tableNameRelated = '{{%article}}';

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
            ],
            $this->tableOptions
        );

        
        $this->addPrimaryKey('pk-article_lang', $this->tableName, ['model_id', 'language']);

        $this->addForeignKey(
            'fk-article_lang-model_id-article-id',
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

