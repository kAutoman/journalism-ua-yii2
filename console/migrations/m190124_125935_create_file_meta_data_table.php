<?php
namespace console\migrations;

use console\components\Migration;

class m190124_125935_create_file_meta_data_table extends Migration
{
    const FK_FPM = 'fk-fpm_meta_data-file_id-fpm_file-id';
    public $table = '{{%fpm_meta_data}}';
    public $refTable = '{{%fpm_file}}';

    public function safeUp()
    {
        $this->createTable($this->table, [
            'file_id' => $this->integer()->notNull(),
            'language' => $this->string(16)->notNull()->comment('Language'),
            'title' => $this->string()->notNull(),
            'alt' => $this->string()->notNull(),
        ], $this->tableOptions);

        $this->addForeignKey(
            self::FK_FPM,
            $this->table,
            'file_id',
            $this->refTable,
            'id',
            'CASCADE',
            'RESTRICT'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey(self::FK_FPM, $this->table);
        $this->dropTable($this->table);
    }
}
