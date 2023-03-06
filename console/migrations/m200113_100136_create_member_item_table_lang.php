<?php

namespace console\migrations;

use console\components\Migration;

/**
 * Class m200113_100136_create_member_item_table_lang migration
 */
class m200113_100136_create_member_item_table_lang extends Migration
{
    /**
     * Migration related table name
     */
    public $tableName = '{{%member_item_lang}}';

    /**
     * main table name, to make constraints
     */
    public $tableNameRelated = '{{%member_item}}';

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

                'label' => $this->string()->notNull()->comment('Label'),
                'content' => $this->text()->null()->comment('Content'),
            ],
            $this->tableOptions
        );

        
        $this->addPrimaryKey('pk-member_item_lang', $this->tableName, ['model_id', 'language']);

        $this->addForeignKey(
            'fk-member_item_lang-model_id-member_item-id',
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

