<?php

namespace common\modules\faq\migrations;

use console\components\Migration;

/**
 * Class m191112_150055_create_faq_table_lang migration
 */
class m191112_150055_create_faq_table_lang extends Migration
{
    /**
     * Migration related table name
     */
    public $tableName = '{{%faq_lang}}';

    /**
     * main table name, to make constraints
     */
    public $tableNameRelated = '{{%faq}}';

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

                'question' => $this->string()->notNull()->comment('Question'),
                'answer' => $this->text()->notNull()->comment('Answer'),
            ],
            $this->tableOptions
        );

        
        $this->addPrimaryKey('pk-faq_lang', $this->tableName, ['model_id', 'language']);

        $this->addForeignKey(
            'fk-faq_lang-model_id-faq-id',
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

