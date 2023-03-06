<?php

namespace console\migrations;

use yii\console\ExitCode;
use console\components\Migration;

/**
 * Class m180830_144711_remove_legacy_configuration_tables migration
 */
class m180830_144711_remove_legacy_configuration_tables extends Migration
{
    const FK = 'fk-configuration_translation-model_id-configuration-id';
    public $table = '{{%configuration}}';
    public $tableI18n = '{{%configuration_translation}}';

    public function safeUp()
    {
        if ($this->tableExist($this->tableI18n)) {
            $this->dropForeignKey(self::FK, $this->tableI18n);
            $this->dropTable($this->tableI18n);
        }
        if ($this->tableExist($this->table)) {
            $this->dropTable($this->table);
        }
    }

    public function safeDown()
    {
        return ExitCode::OK; // No support for down method.
    }
}
