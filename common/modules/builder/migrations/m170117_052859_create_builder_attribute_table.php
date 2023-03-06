<?php

namespace common\modules\builder\migrations;

use console\components\Migration;

/**
 * Class m170117_052859_create_builder_attribute_table migration
 */
class m170117_052859_create_builder_attribute_table extends Migration
{
    /**
     * migration table name
     */
    public $tableName = '{{%builder_attribute}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'builder_id' => $this->integer()->notNull()->comment('Builder'),
                'attribute' => $this->string()->notNull()->comment('Attribute'),

                'value' => $this->text()->null()->comment('Value'),

                'created_at' => $this->integer()->notNull()->comment('Created At'),
                'updated_at' => $this->integer()->notNull()->comment('Updated At'),
            ],
            $this->tableOptions
        );
        $this->addForeignKey(
            'fk-builder_attribute-builder_id-builder-id',
            $this->tableName,
            'builder_id',
            '{{%builder}}',
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
        $this->dropForeignKey('fk-builder_attribute-builder_id-builder-id', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
