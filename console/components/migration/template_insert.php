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
    public $table = '{{%<?= $tableName; ?>}}';

    public function safeUp()
    {
        $time = time();
        $this->insert($this->table, [
            'id' => 'id',
            'type' => Configuration::TYPE_STRING,
            'description' => 'Description',
            'value' => '',
            'created_at' => $time,
            'updated_at' => $time,
        ]);
    }

    public function safeDown()
    {
        $this->delete($this->table, ['id' => 'id']);
    }
}
