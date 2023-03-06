<?php

namespace console\migrations;

use console\components\Migration;

class m200310_102715_alter_table_competition_request extends Migration
{
    public $table = '{{%competition_request}}';

    public function safeUp()
    {
        $this->addColumn($this->table, 'moderator_comment', $this->text()->null());
        $this->addColumn($this->table, 'email_message', $this->text()->null());
    }

    public function safeDown()
    {
        $this->dropColumn($this->table, 'moderator_comment');
        $this->dropColumn($this->table, 'email_message');
    }
}
