<?php

namespace common\modules\builder\migrations;

use console\components\Migration;

/**
 * Class m170117_052626_create_builder_table migration
 */
class m170117_052626_create_builder_table extends Migration
{
    /**
     * migration table name
     */
    public $tableName = '{{%builder}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'language' => $this->string(5)->notNull()->comment('Language'),
                'builder_model_class' => $this->string()->notNull()->comment('Common builder model class name'),

                'target_class' => $this->string()->notNull()->comment('Target class'),
                'target_id' => $this->integer()->notNull()->comment('Target entity ID'),
                'target_sign' => $this->string()->null()->comment('Target sign'),
                'target_attribute' => $this->string()->notNull()->comment('Target attribute'),

                'position' => $this->integer()->notNull()->defaultValue(0)->comment('Position'),
                'published' => $this->boolean()->defaultValue(true)->comment('Is published'),

                'tag_level' => $this->tinyInteger()->notNull()->defaultValue(2)->comment('Tag level'),
                'component_name' => $this->string()->null()->comment('Custom component name'),

                'created_at' => $this->integer()->notNull()->comment('Created At'),
                'updated_at' => $this->integer()->notNull()->comment('Updated At'),
            ],
            $this->tableOptions
        );

        $this->createIndex('idx-builder-language', $this->tableName, 'language');
        $this->createIndex('idx-builder-position', $this->tableName, 'position');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
