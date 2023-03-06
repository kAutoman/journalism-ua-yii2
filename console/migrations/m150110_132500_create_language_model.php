<?php

namespace console\migrations;

use yii\db\Schema;
use console\components\Migration;

/**
 * Class m150110_132500_create_language_model migration
 */
class m150110_132500_create_language_model extends Migration
{
    /**
     * migration table name
     */
    public $tableName = '{{%language}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'label' => $this->string(20)->notNull()->comment('Label'),
                'code' => $this->string(2)->notNull()->unique()->comment('Code'),
                'region' => $this->string(2)->notNull()->comment('Region'),
                'published' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(1)->comment('Published'),
                'position' => $this->integer(1)->unsigned()->notNull()->defaultValue(0)->comment('Position'),
                'is_default' => $this->smallInteger(1)->notNull()->defaultValue(0)->comment('Is language default'),
                'created_at' => $this->integer()->notNull()->comment('Created at'),
                'updated_at' => $this->integer()->notNull()->comment('Updated at'),
            ],
            $this->tableOptions
        );

        $this->insert($this->tableName, [
            'label' => 'UA',
            'code' => 'uk',
            'region' => 'UA',
            'published' => 1,
            'position' => 1,
            'is_default' => 1,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert($this->tableName, [
            'label' => 'RU',
            'code' => 'ru',
            'region' => 'UA',
            'published' => 1,
            'position' => 2,
            'is_default' => 0,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert($this->tableName, [
            'label' => 'EN',
            'code' => 'en',
            'region' => 'US',
            'published' => 1,
            'position' => 3,
            'is_default' => 0,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
