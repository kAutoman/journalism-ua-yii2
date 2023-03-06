<?php
/**
 * This view is used by console/controllers/MigrateController.php
 * The following variables are available in this view:
 */
/* @var $className string the new migration class name */
/* @var $tableName string the new migration table name */

echo "<?php\n";
function makeFkTableName($delimiter, $tableName) {
    $tableNameArr = explode($delimiter, $tableName);
    $fkTableNameArr = [];
    foreach ($tableNameArr as $value) {
        array_push($fkTableNameArr, $value[0]);
    };

    return implode($delimiter, $fkTableNameArr);
}
$fkTableName = $tableName;
if (strlen($tableName) > 19) {
    if (strpos($tableName, '_') !== false) {
        $fkTableName = makeFkTableName('_', $tableName);
    } elseif (strpos($tableName, '-') !== false) {
        $fkTableName = makeFkTableName('-', $tableName);
    }
}
?>

namespace console\migrations;

use console\components\Migration;

class <?= $className ?> extends Migration
{
    const PK = 'pk-<?= $fkTableName . '_translation' ?>';
    const FK = 'fk-<?= $fkTableName . '_translation' ?>-model_id-<?= $fkTableName ?>-id';
    public $table = '{{%<?= $tableName . '_translation' ?>}}';
    public $tableMain = '{{%<?= $tableName ?>}}';

    public function safeUp()
    {
        $this->createTable($this->table, [
            'model_id' => $this->integer()->notNull(),
            'language' => $this->string(16)->notNull(),
            // examples:
            // 'label' => $this->string(),
            // 'content' => $this->text(),
        ], $this->tableOptions);

        $this->addPrimaryKey(self::PK, $this->table, ['model_id', 'language']);
        $this->addForeignKey(self::FK, $this->table, 'model_id', $this->tableMain, 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable($this->table);
    }
}
