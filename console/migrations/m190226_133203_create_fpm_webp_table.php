<?php

namespace console\migrations;

use console\components\Migration;

class m190226_133203_create_fpm_webp_table extends Migration
{
    public $table = '{{%fpm_webp}}';
    public $refTable = '{{%fpm_file}}';

    const PK_FPM = 'pk-fpm_webp-file_id';
    const FK_FPM = 'fk-fpm_webp-file_id-fpm_file-id';

    public function safeUp()
    {
        $this->createTable($this->table, [
            'file_id' => $this->integer()->notNull()->unique(),
            'name' => $this->string()->notNull(),
        ], $this->tableOptions);

        $this->addPrimaryKey(self::PK_FPM, $this->table, ['file_id']);
        $this->addForeignKey(
            self::FK_FPM,
            $this->table,
            'file_id',
            $this->refTable,
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey(self::FK_FPM, $this->table);
        $this->dropTable($this->table);
    }
}
