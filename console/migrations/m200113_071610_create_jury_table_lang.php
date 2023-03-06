<?php

namespace console\migrations;

use console\components\Migration;

/**
 * Class m200113_071610_create_jury_table_lang migration
 */
class m200113_071610_create_jury_table_lang extends Migration
{
    /**
     * Migration related table name
     */
    public $tableName = '{{%jury_lang}}';

    /**
     * main table name, to make constraints
     */
    public $tableNameRelated = '{{%jury}}';

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

                'name' => $this->string()->notNull()->comment('Name'),
                'description' => $this->text()->notNull()->comment('Description'),
            ],
            $this->tableOptions
        );

        
        $this->addPrimaryKey('pk-jury_lang', $this->tableName, ['model_id', 'language']);

        $this->addForeignKey(
            'fk-jury_lang-model_id-jury-id',
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

