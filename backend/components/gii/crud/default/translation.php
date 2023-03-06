<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */
/* @var $multiLanguageModel */
/* @var $translationModel boolean */
/* @var $behaviors string[] list of behaviors */
/* @var $translationAttributes string[] list of translated attributes */

$commonClassName = "Common{$className}";
echo "<?php\n";
?>

namespace <?= $generator->ns ?>;

use Yii;
use common\models\<?= $className; ?> as <?= $commonClassName; ?>;

/**
 * This is the model class for table "<?= $generator->generateTableName($tableName) ?>".
 *
<?php foreach ($tableSchema->columns as $column): ?>
 * @property <?= "{$column->phpType} \${$column->name}\n" ?>
<?php endforeach; ?>
 */
class <?= $className ?> extends <?= $commonClassName; ?><?= "\n" ?>
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '<?= $generator->generateTableName($tableName) ?>';
    }

    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
<?php foreach ($labels as $name => $label): ?>
            <?= "'$name' => " . $generator->generateStringWithTable($tableSchema, $label) . ",\n" ?>
<?php endforeach; ?>
        ];
    }

    /**
    * @inheritdoc
    */
    public function rules()
    {
        return [<?= "\n            " . implode(",\n            ", $rules) . ",\n        " ?> ];
    }
}
