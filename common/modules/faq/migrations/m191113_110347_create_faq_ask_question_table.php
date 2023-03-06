<?php

namespace common\modules\faq\migrations;

use console\components\Migration;

/**
 * Class m191113_110347_create_faq_ask_question_table migration
 */
class m191113_110347_create_faq_ask_question_table extends Migration
{
    /**
     * migration table name
     */
    public $tableName = '{{%faq_ask_question}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'name' => $this->string()->notNull()->comment('Name'),
                'email' => $this->string()->null()->comment('Email'),
                'phone' => $this->string()->null()->comment('Phone'),
                'question' => $this->text()->null()->comment('Question'),
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
