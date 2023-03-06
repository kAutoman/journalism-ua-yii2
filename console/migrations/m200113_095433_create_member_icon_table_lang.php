<?php

namespace console\migrations;

use console\components\Migration;

/**
 * Class m200113_095433_create_member_icon_table_lang migration
 */
class m200113_095433_create_member_icon_table_lang extends Migration
{
    /**
     * Migration related table name
     */
    public $tableName = '{{%member_icon_lang}}';

    /**
     * main table name, to make constraints
     */
    public $tableNameRelated = '{{%member_icon}}';

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

                'description' => $this->text()->notNull()->comment('Description'),
            ],
            $this->tableOptions
        );

        
        $this->addPrimaryKey('pk-member_icon_lang', $this->tableName, ['model_id', 'language']);

        $this->addForeignKey(
            'fk-member_icon_lang-model_id-member_icon-id',
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

