<?php
/**
 * This view is used by console/controllers/MigrateController.php
 * The following variables are available in this view:
 */
/* @var $className string the new migration class name */
/* @var $tableName string the new migration table name */

echo "<?php\n";
?>

namespace console\migrations;

use console\components\Migration;

class <?= $className ?> extends Migration
{
    public $table = '{{%<?= $tableName . '_translation' ?>}}';
    public $tableI18n = '{{%<?= $tableName ?>}}';

    public function safeUp()
    {
        $this->addColumn($this->table, 'column_name', $this->string()->null());
    }

    public function safeDown()
    {
        $this->dropColumn($this->table, 'column_name');
    }
}
