<?php
namespace console\migrations;

use console\components\Migration;

class m190904_143707_add_column_to_user_table extends Migration
{
    public $table = '{{%user}}';

    public function safeUp()
    {
        $this->addColumn($this->table, 'expire_at', $this->integer()->null()->comment('token expiration time'));
    }

    public function safeDown()
    {
        $this->dropColumn($this->table, 'expire_at');
    }
}
