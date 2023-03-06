<?php

namespace common\modules\seo\migrations;

use yii\db\Schema;
use console\components\Migration;

/**
 * Class m151211_082711_create_robots_table migration
 */
class m151211_082711_create_robots_table extends Migration
{
    /**
     * migration table name
     */
    public $tableName = '{{%robots}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'text' => $this->text()->defaultValue(null)->comment('Text'),
            ],
            $this->tableOptions
        );

        $this->insert($this->tableName, [
               'text' => 'User-agent: *
Disallow: /'
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
