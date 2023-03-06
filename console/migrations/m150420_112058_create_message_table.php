<?php

namespace console\migrations;

use yii\db\Schema;
use console\components\Migration;

/**
 * Class m150420_112058_create_message_table migration
 */
class m150420_112058_create_message_table extends Migration
{
    /**
     * migration table name
     */
    public $tableName = '{{%message}}';

    /**
     * main table name, to make constraints
     */
    public $tableNameRelated = '{{%source_message}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->integer(),
                'language' => $this->string(16)->notNull(),
                'translation' => $this->text()->defaultValue(null),
            ],
            $this->tableOptions
        );
        $this->addPrimaryKey('message_pk', $this->tableName, ['id', 'language']);
        $this->addForeignKey(
            'fk-message-id-source_message-id',
            $this->tableName,
            'id',
            $this->tableNameRelated,
            'id',
            'CASCADE',
            'RESTRICT'
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
