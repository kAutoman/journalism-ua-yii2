<?php

namespace common\modules\mailer\migrations;

use yii\db\Migration;

/**
 * Class m180914_231339_create_mailer_setting
 */
class m180914_231339_create_mailer_setting extends Migration
{
    public $tableName = '{{%mailer_setting}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'label' => $this->string()->notNull()->comment('Label'),
            'subject' => $this->string()->notNull()->comment('Subject'),
            'template' => $this->text()->null()->comment('Template'),
            'use_default' => $this->boolean()->notNull()->defaultValue(false)->comment('Use default settings'),
            'smtp_host' => $this->string()->notNull()->comment('SMTP host'),
            'smtp_port' => $this->integer()->notNull()->comment('SMTP port'),
            'smtp_encryption' => $this->string()->notNull()->comment('SMTP encryption'),
            'auth' => $this->boolean()->notNull()->defaultValue(true)->comment('Auth'),
            'smtp_username' => $this->string()->null()->comment('SMTP username'),
            'smtp_password' => $this->string()->null()->comment('SMTP password'),
            'send_from' => $this->string()->notNull()->comment('Send from'),
            'send_to' => $this->string()->notNull()->comment('Send to'),
            'send_to_cc' => $this->text()->null()->comment('Send to cc'),
            'send_to_bcc' => $this->text()->null()->comment('Send to bcc'),
            'is_default' => $this->boolean()->notNull()->defaultValue(false)->comment('Is default'),
            'created_at' => $this->integer()->notNull()->comment('Created At'),
            'updated_at' => $this->integer()->notNull()->comment('Updated At'),
        ]);

        $this->insert($this->tableName, [
            'id' => 1,
            'label' => 'Default settings',
            'template' => 'Default template',
            'subject' => 'Default subject',
            'use_default' => 1,
            'smtp_host' => 'smtp.gmail.com',
            'smtp_port' => 465,
            'smtp_encryption' => 'ssl',
            'auth' => true,
            'smtp_username' => 'devmailroman@gmail.com',
            'smtp_password' => 'qwerty1857',
            'send_from' => 'devmailroman@gmail.com',
            'send_to' => 'example@mail.com',
            'send_to_cc' => null,
            'send_to_bcc' => null,
            'is_default' => true,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete($this->tableName, ['id' => 1]);
        $this->dropTable($this->tableName);
    }
}
