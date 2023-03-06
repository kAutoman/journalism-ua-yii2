<?php
/**
 * This view is used by console/controllers/MigrateController.php
 * The following variables are available in this view:
 */
use backend\components\gii\migration\Field;
use backend\components\gii\migration\ForeignKey;

/* @var $this yii\web\View */
/* @var $generator \backend\components\gii\migration\Generator */

$className = $generator->migrationName;
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
     * migration table name
     */
    public $tableName = '{{%<?= $generator->tableName ?>}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(
            $this->tableName,
            [
<?php foreach ($generator->fields as $field) :
    $field = new Field($field);
        if (!$field->isLang) :
?>
                '<?= $field->name ?>' => $this-><?= $field->getTypeOutput() ?><?= $field->getNullOutput() ?><?= $field->getDefaultValueOutput() ?>
<?= $field->getUnsignedOutput() ?><?= $field->getUniqueOutput() ?><?= $field->getCommentOutput() ?>,
<?php endif; ?>
<?php endforeach; ?>
            ],
            $this->tableOptions
        );
<?php foreach ($generator->fields as $field) :
        $field = new Field($field);
        if ($field->isIndex && !$field->isLang) :
    ?>
        $this->createIndex('idx-<?= $generator->tableName ?>-<?= $field->name ?>', '<?= $generator->tableName ?>', '<?= $field->name ?>', <?= $field->isUnique ? 'true' : 'false' ?>);
<?php endif;
endforeach; ?>
<?php foreach ($generator->foreignKeys as $key) :
        $key = new ForeignKey($key);
    ?>
        $this->addForeignKey(
            '<?= $key->getName($generator->tableName) ?>',
            $this->tableName,
            '<?= $key->fieldName ?>',
            '{{%<?= $key->relTableName ?>}}',
            '<?= $key->relTableFieldName ?>',
            '<?= $key->getDeleteActionLabel() ?>',
            '<?= $key->getUpdateActionLabel() ?>'
        );
<?php endforeach; ?>
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
<?php foreach ($generator->foreignKeys as $key) :
        $key = new ForeignKey($key);
    ?>
        $this->dropForeignKey('<?= $key->getName($generator->tableName) ?>', $this->tableName);
<?php endforeach; ?>
        $this->dropTable($this->tableName);
    }
}
