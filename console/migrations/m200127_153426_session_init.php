<?php

namespace console\migrations;

use console\components\Migration;

class m200127_153426_session_init extends Migration
{
    public const FK_USER = 'fk-session-user_id-user-id';
    public const IX_SESSION = 'fk-session-expire';

    public $tableUser = '{{%user}}';
    public $tableSession = '{{%session}}';

    public function safeUp()
    {
        $this->createTable($this->tableSession, [
            'id' => $this->string()->notNull(),
            'user_id' => $this->integer()->defaultValue(null),
            'expire' => $this->integer(),
            'data' => $this->binary(),
            'PRIMARY KEY ([[id]])',
        ], $this->tableOptions);

        $this->addForeignKey(self::FK_USER, $this->tableSession, 'user_id', $this->tableUser, 'id', 'CASCADE', 'CASCADE');
        $this->createIndex(self::IX_SESSION, $this->tableSession, 'expire', false);
    }

    public function safeDown()
    {
        $this->dropForeignKey(self::FK_USER, $this->tableSession);
        $this->dropTable('{{%session}}');
    }
}
