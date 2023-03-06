<?php

namespace common\modules\mailer\migrations;

use console\components\Migration;
use yii\console\ExitCode;

class m180915_211124_drop_legacy_postman_table extends Migration
{
    public $table = '{{%postman_letter}}';

    public function safeUp()
    {
        if ($this->tableExist($this->table)) {
            $this->dropTable($this->table);
        }
    }

    public function safeDown()
    {
        return ExitCode::OK;
    }
}
