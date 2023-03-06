<?php

namespace console\migrations;

use console\components\Migration;

class m191029_143132_create_table_page_lang extends Migration
{
    const PK = 'pk-page_lang';
    const FK = 'fk-page_lang-model_id-page-id';
    public $table = '{{%page_lang}}';
    public $tableMain = '{{%page}}';

    public function safeUp()
    {
        $this->createTable($this->table, [
            'model_id' => $this->integer()->notNull(),
            'language' => $this->string(16)->notNull(),
            // examples:
            'label' => $this->string()->notNull(),
            'content' => $this->text()->null(),
        ], $this->tableOptions);

        $this->addPrimaryKey(self::PK, $this->table, ['model_id', 'language']);
        $this->addForeignKey(self::FK, $this->table, 'model_id', $this->tableMain, 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable($this->table);
    }
}
