<?php

namespace common\modules\mailer\migrations;

use console\components\Migration;

class m180915_114944_create_table_mailer_letter extends Migration
{
    public $table = '{{%mailer_letter}}';
    public $tableSetting = '{{%mailer_setting}}';

    public function safeUp()
    {
        $this->createTable($this->table, [
            'id' => $this->primaryKey(),
            'connection_id' => $this->integer()->null()->comment('Connection'),
            'date_create' => $this->integer()->notNull()->comment('Date create'),
            'date_update' => $this->integer()->null()->comment('Date update'),
            'status' => $this->integer()->notNull()->comment('Status'),
            'subject' => $this->string()->notNull()->comment('Subject'),
            'body' => $this->text()->null()->comment('Letter body'),
            'recipients' => $this->text()->notNull()->comment('Recipients'),
            'attachments' => $this->text()->null()->comment('Attachments'),
        ], $this->tableOptions);
        $this->createIndex('idx-letter-connection_id', $this->table, 'connection_id');
        $this->addForeignKey(
            'fk-letter-connection_id-setting-id',
            $this->table,
            'connection_id',
            $this->tableSetting,
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropTable($this->table);
    }
}
