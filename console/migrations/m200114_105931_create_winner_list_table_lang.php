<?php

namespace console\migrations;

use console\components\Migration;

/**
 * Class m200114_105931_create_winner_list_table_lang migration
 */
class m200114_105931_create_winner_list_table_lang extends Migration
{
    /**
     * Migration related table name
     */
    public $tableName = '{{%winner_list_lang}}';

    /**
     * main table name, to make constraints
     */
    public $tableNameRelated = '{{%winner_list}}';

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
                'publication_label' => $this->string()->notNull()->comment('Publication label'),
            ],
            $this->tableOptions
        );

        
        $this->addPrimaryKey('pk-winner_list_lang', $this->tableName, ['model_id', 'language']);

        $this->addForeignKey(
            'fk-winner_list_lang-model_id-winner_list-id',
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

