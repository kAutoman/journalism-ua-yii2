<?php
/**
 * This view is used by console/controllers/MigrateController.php
 * The following variables are available in this view:
 */
use backend\components\gii\migration\Field;

/* @var $className string the new migration class name */
/* @var $tableName string the new migration table name */
/* @var $generator \backend\components\gii\migration\Generator */

$className = $generator->migrationName . '_lang';
$tableName = $generator->tableName;
echo "<?php\n";
?>

namespace console\migrations;

use console\components\Migration;

/**
 * Class <?= $className ?> migration
 */
class <?= $className ?> extends Migration
{
    /**
     * Migration related table name
     */
    public $tableName = '{{%<?= $tableName . '_lang' ?>}}';

    /**
     * main table name, to make constraints
     */
    public $tableNameRelated = '{{%<?= $tableName ?>}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(
            $this->tableName,
            [
                'model_id' => $this->integer()->notNull()->comment('Related model id'),
                'language' => $this->string(16)->notNull()->comment('Language'),

<?php foreach ($generator->fields as $field):
        $field = new Field($field);
        if ($field->isLang) {
    ?>
                '<?= $field->name ?>' => $this-><?= $field->getTypeOutput() ?><?= $field->getNullOutput() ?><?= $field->getDefaultValueOutput() ?><?= $field->getUnsignedOutput() ?><?= $field->getUniqueOutput() ?><?= $field->getCommentOutput() ?>,
<?php }
    endforeach; ?>
            ],
            $this->tableOptions
        );

        <?php
            $fkTableName = $tableName;
            if (strlen($tableName) > 18) {
                if (strpos($tableName, '_') !== false) {
                    $fkTableName = makeFkTableName('_', $tableName);
                } elseif (strpos($tableName, '-') !== false) {
                    $fkTableName = makeFkTableName('-', $tableName);
                }
            }
        ?>

        $this->addPrimaryKey('pk-<?= $fkTableName . '_lang' ?>', $this->tableName, ['model_id', 'language']);

        $this->addForeignKey(
            'fk-<?= $fkTableName . '_lang' ?>-model_id-<?= $fkTableName ?>-id',
            $this->tableName,
            'model_id',
            $this->tableNameRelated,
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}

<?php
    function makeFkTableName($delimiter, $tableName) {
        $tableNameArr = explode($delimiter, $tableName);
        $fkTableNameArr = [];
        foreach ($tableNameArr as $value) {
            array_push($fkTableNameArr, $value[0]);
        };

        return implode($delimiter, $fkTableNameArr);
    }
?>
