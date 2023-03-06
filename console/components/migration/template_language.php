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
    const FK = 'fk-<?= $tableName ?>_translation-model_id-<?= $tableName ?>-id';
    public $table = '{{%<?= $tableName ?>_translation}}';
    public $tableMain = '{{%<?= $tableName ?>}}';

    public function safeUp()
    {
        $this->createTable($this->table, [
            'model_id' => $this->integer()->notNull(),
            'language' => $this->string(16)->notNull(),
            // examples:
            // 'label' => $this->string()->defaultValue(null),
            // 'content' => $this->text()->defaultValue(null),
        ], $this->tableOptions);

        $this->addPrimaryKey(null, $this->table, ['model_id', 'language']);
        $this->addForeignKey(self::FK, $this->table, 'model_id', $this->tableMain, 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable($this->table);
    }
}
