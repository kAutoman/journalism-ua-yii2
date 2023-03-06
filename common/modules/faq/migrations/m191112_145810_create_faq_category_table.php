<?php

namespace common\modules\faq\migrations;

use console\components\Migration;

/**
 * Class m191112_145810_create_faq_category_table migration
 */
class m191112_145810_create_faq_category_table extends Migration
{
    /**
     * migration table name
     */
    public $tableName = '{{%faq_category}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'alias' => $this->string()->null()->unique()->comment('Alias'),
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
