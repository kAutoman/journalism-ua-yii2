<?php

namespace console\migrations;

use console\components\Migration;

/**
 * Class m200312_124609_create_competition_request_rating_table migration
 */
class m200312_124609_create_competition_request_rating_table extends Migration
{
    /**
     * migration table name
     */
    public $tableName = '{{%competition_request_rating}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'request_id' => $this->integer()->null()->comment('Request ID'),
                'user_id' => $this->integer()->null()->comment('User ID'),
                'rating' => $this->integer()->null()->comment('Rating'),
                'created_at' => $this->integer()->notNull()->comment('Created At'),
                'updated_at' => $this->integer()->notNull()->comment('Updated At'),
            ],
            $this->tableOptions
        );
        $this->addForeignKey(
            'fk-competition_request_rating-request_id-competition_request-id',
            $this->tableName,
            'request_id',
            '{{%competition_request}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-competition_request_rating-user_id-user-id',
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
        $this->dropForeignKey('fk-competition_request_rating-request_id-competition_request-id', $this->tableName);
        $this->dropForeignKey('fk-competition_request_rating-user_id-user-id', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
