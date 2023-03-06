<?php

namespace console\migrations;

use yii\db\Schema;
use console\components\Migration;

/**
 * Class m150420_112051_create_source_message_table migration
 */
class m150420_112051_create_source_message_table extends Migration
{
    /**
     * migration table name
     */
    public $tableName = '{{%source_message}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'category' => $this->string(32)->notNull(),
                'message' => $this->text()->defaultValue(null),
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
