<?php
/**
 * This is the template for generating the model class of a specified table.
 */
use backend\components\gii\migration\Generator;

/* @var $this yii\web\View */
/* @var $generator Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */
/* @var bool $multiLanguageModel */
/* @var $translationAttributes string[] list of translated attributes */
/* @var $behaviors string[] list of behaviors */
echo "<?php\n";
?>

namespace common\models\lang;

use common\components\model\ActiveRecord;

/**
 * This is the model class for table "<?= $generator->generateTableName($tableName) ?>".
 *
<?php foreach ($tableSchema->columns as $column): ?>
 * @property <?= "{$column->phpType} \${$column->name}\n" ?>
<?php endforeach; ?>
 */
class <?= $className ?> extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '<?= $generator->generateTableName($tableName) ?>';
    }
}
