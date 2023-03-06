<?php

namespace console\migrations;

use console\components\Migration;

/**
 * Class m190910_100548_create_user_log_table migration
 */
class m180910_100548_create_user_log_table extends Migration
{
    /**
     * migration table name
     */
    public $tableName = '{{%user_log}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'action' => $this->string()->notNull()->comment('Action'),
                'user_id' => $this->integer()->null()->comment('User'),
                'model_class' => $this->string()->notNull()->comment('Model class'),
                'entity_id' => $this->string()->null()->defaultValue('0')->comment('Entity id'),
                'content_before' => $this->text()->null()->comment('Content before'),
                'user_info' => $this->text()->null()->comment('User info'),
                'content_after' => $this->text()->null()->comment('Content after'),
                'created_at' => $this->integer()->notNull()->comment('Created At'),
                'updated_at' => $this->integer()->notNull()->comment('Updated At'),
            ],
            $this->tableOptions
        );
        $this->addForeignKey(
            'fk-user_log-user_id-user-id',
            $this->tableName,
            'user_id',
            '{{%user}}',
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
        $this->dropForeignKey('fk-user_log-user_id-user-id', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
