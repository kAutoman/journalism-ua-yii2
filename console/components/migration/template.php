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
        $this->createTable($this->table, [
            'id' => $this->primaryKey(),
            'label' => $this->string()->notNull(),
            'content' => $this->text()->defaultValue(null),
            'published' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(1),
            'position' => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $this->tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable($this->table);
    }
}
