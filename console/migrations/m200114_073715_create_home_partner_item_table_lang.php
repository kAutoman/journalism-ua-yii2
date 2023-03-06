<?php

namespace console\migrations;

use console\components\Migration;

/**
 * Class m200114_073715_create_home_partner_item_table_lang migration
 */
class m200114_073715_create_home_partner_item_table_lang extends Migration
{
    /**
     * Migration related table name
     */
    public $tableName = '{{%home_partner_item_lang}}';

    /**
     * main table name, to make constraints
     */
    public $tableNameRelated = '{{%home_partner_item}}';

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
            ],
            $this->tableOptions
        );

        
        $this->addPrimaryKey('pk-home_partner_item_lang', $this->tableName, ['model_id', 'language']);

        $this->addForeignKey(
            'fk-home_partner_item_lang-model_id-home_partner_item-id',
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

