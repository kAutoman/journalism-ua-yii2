<?php

namespace console\migrations;

use yii\db\Schema;
use console\components\Migration;

/**
 * Class m150210_140616_create_user_auth_table migration
 */
class m150210_140616_create_user_auth_table extends Migration
{
    /**
     * migration table name
     */
    public $tableName = '{{%user_auth}}';

    /**
     * related table name, to make constraints
     */
    public $tableNameRelated = '{{%user}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'user_id' => $this->integer()->notNull()->comment('User'),
                'source' => $this->string()->notNull()->comment('Source'),
                'source_id' => $this->string()->notNull()->comment('Source ID'),
                'created_at' => $this->integer()->notNull()->comment('Created at'),
                'updated_at' => $this->integer()->notNull()->comment('Updated at'),
            ],
            $this->tableOptions
        );

        $this->addForeignKey(
            'fk-user_auth-user_id-user-id',
            $this->tableName,
            'user_id',
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
