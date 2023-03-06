<?php

namespace common\modules\faq\migrations;

use console\components\Migration;

/**
 * Class m191112_150055_create_faq_table migration
 */
class m191112_150055_create_faq_table extends Migration
{
    /**
     * migration table name
     */
    public $tableName = '{{%faq}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'category_id' => $this->integer()->notNull()->comment('Category'),
                'published' => $this->boolean()->notNull()->defaultValue(true)->comment('Published'),
                'position' => $this->integer()->notNull()->defaultValue(0)->comment('Position'),
                'created_at' => $this->integer()->notNull()->comment('Created At'),
                'updated_at' => $this->integer()->notNull()->comment('Updated At'),
            ],
            $this->tableOptions
        );
        $this->createIndex('idx-faq-category_id', 'faq', 'category_id', false);
        $this->addForeignKey(
            'fk-faq-category_id-faq_category-id',
            $this->tableName,
            'category_id',
            '{{%faq_category}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-faq-category_id-faq_category-id', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
