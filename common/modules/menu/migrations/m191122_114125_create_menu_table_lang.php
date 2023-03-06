<?php

namespace common\modules\menu\migrations;

use console\components\Migration;

/**
 * Class m191122_114125_create_menu_table_lang migration
 */
class m191122_114125_create_menu_table_lang extends Migration
{
    /**
     * Migration related table name
     */
    public $tableName = '{{%menu_lang}}';

    /**
     * main table name, to make constraints
     */
    public $tableNameRelated = '{{%menu}}';

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

        
        $this->addPrimaryKey('pk-menu_lang', $this->tableName, ['model_id', 'language']);

        $this->addForeignKey(
            'fk-menu_lang-model_id-menu-id',
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

