<?php

namespace console\migrations;

use console\components\Migration;

/**
 * Class m151223_143528_create_entity_to_file_table migration
 */
class m151223_143528_create_entity_to_file_table extends Migration
{
    /**
     * migration table name
     */
    public $tableName = '{{%entity_to_file}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'entity_model_name' => $this->string()->notNull(),
                'entity_model_id' => $this->integer()->notNull(),
                'file_id' => $this->integer()->notNull(),
                'temp_sign' => $this->string()->notNull()->defaultValue(''),
                'position' => $this->integer()->notNull()->defaultValue(0),
                'attribute' => $this->string()->defaultValue(null),
            ]
        );

        $this->addForeignKey(
            'fk_entity_file_id_to_fpm_file_table',
            $this->tableName,
            'file_id',
            \metalguardian\fileProcessor\helpers\FPM::getTableName(),
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * commands will be executed in transaction
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
