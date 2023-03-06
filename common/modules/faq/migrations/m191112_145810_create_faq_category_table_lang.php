<?php

namespace common\modules\faq\migrations;

use console\components\Migration;

/**
 * Class m191112_145810_create_faq_category_table_lang migration
 */
class m191112_145810_create_faq_category_table_lang extends Migration
{
    /**
     * Migration related table name
     */
    public $tableName = '{{%faq_category_lang}}';

    /**
     * main table name, to make constraints
     */
    public $tableNameRelated = '{{%faq_category}}';

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

        
        $this->addPrimaryKey('pk-faq_category_lang', $this->tableName, ['model_id', 'language']);

        $this->addForeignKey(
            'fk-faq_category_lang-model_id-faq_category-id',
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

