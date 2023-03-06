<?php

namespace console\migrations;

use console\components\Migration;

class m200115_075729_alter_table_menu extends Migration
{
    public $table = '{{%menu}}';

    public $pageTable = '{{%page}}';

    public function safeUp()
    {
        $this->addColumn($this->table, 'page_id', $this->integer()->null());
        
        $this->addForeignKey(
            'fk-menu-page_id-page-id',
            $this->table,
            'page_id',
            $this->pageTable,
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropColumn($this->table, 'page_id');
    }
}
