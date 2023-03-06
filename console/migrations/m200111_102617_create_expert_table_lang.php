<?php

namespace console\migrations;

use console\components\Migration;

/**
 * Class m200111_102617_create_expert_table_lang migration
 */
class m200111_102617_create_expert_table_lang extends Migration
{
    /**
     * Migration related table name
     */
    public $tableName = '{{%expert_lang}}';

    /**
     * main table name, to make constraints
     */
    public $tableNameRelated = '{{%expert}}';

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
                'staff' => $this->text()->notNull()->comment('Staff'),
            ],
            $this->tableOptions
        );

        
        $this->addPrimaryKey('pk-expert_lang', $this->tableName, ['model_id', 'language']);

        $this->addForeignKey(
            'fk-expert_lang-model_id-expert-id',
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

