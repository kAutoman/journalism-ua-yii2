<?php

namespace common\modules\seo\migrations;

use console\components\Migration;

/**
 * Class m191119_111057_create_meta_tags_table migration
 */
class m191119_111057_create_meta_tags_table extends Migration
{
    /**
     * migration table name
     */
    public $tableName = '{{%meta_tags}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'label' => $this->string()->notNull()->comment('Label'),
                'name' => $this->string()->notNull()->unique()->comment('Name'),
                'type' => $this->integer()->null()->comment('Type'),
                'position' => $this->integer()->notNull()->defaultValue(0)->comment('Position'),
            ],
            $this->tableOptions
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
