<?php

namespace console\migrations;

use console\components\Migration;

/**
 * Class m200114_072731_create_home_header_slider_table_lang migration
 */
class m200114_072731_create_home_header_slider_table_lang extends Migration
{
    /**
     * Migration related table name
     */
    public $tableName = '{{%home_header_slider_lang}}';

    /**
     * main table name, to make constraints
     */
    public $tableNameRelated = '{{%home_header_slider}}';

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
                'button_label' => $this->string()->null()->comment('Button label'),
                'button_src' => $this->string()->null()->comment('Button link'),
            ],
            $this->tableOptions
        );

        
        $this->addPrimaryKey('pk-home_header_slider_lang', $this->tableName, ['model_id', 'language']);

        $this->addForeignKey(
            'fk-home_header_slider_lang-model_id-home_header_slider-id',
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

