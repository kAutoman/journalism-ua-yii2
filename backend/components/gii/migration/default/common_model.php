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

namespace common\models;

use common\components\model\ActiveRecord;
<?= isset($behaviors['timestamp']) ? "use yii\\behaviors\\TimestampBehavior;\n" : null; ?>
<?php if ($multiLanguageModel) : ?>
use common\behaviors\TranslatedBehavior;
use common\interfaces\Translatable;
use common\models\lang\<?= $className; ?>Lang;
use lav45\translate\TranslatedTrait;
<?php endif; ?>

/**
 * This is the model class for table "<?= $generator->generateTableName($tableName) ?>".
 *
<?php foreach ($tableSchema->columns as $column): ?>
 * @property <?= "{$column->phpType} \${$column->name}\n" ?>
<?php endforeach; ?>
<?php if (!empty($relations)): ?>
 *
<?php foreach ($relations as $name => $relation): ?>
 * @property <?= $relation[1] . ($relation[2] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?>
<?php endforeach; ?>
<?php endif; ?>
<?php foreach ($generator->imageUploaders as $uploader): ?>
 * @property EntityToFile<?= $uploader['multiple'] ? '[]' : '' ?> $<?= Generator::camelCase($uploader['attribute']) ?><?= "\n" ?>
<?php endforeach; ?>
 */
class <?= $className ?> extends ActiveRecord<?= $multiLanguageModel ? " implements Translatable\n" : "\n" ?>
{
<?php foreach ($generator->imageUploaders as $uploader): ?>
    const <?= Generator::getSaveAttributeConstantName($uploader['attribute']) ?> = '<?= $className . ucfirst($uploader['attribute']) ?>';
<?php endforeach; ?>

<?php if ($multiLanguageModel) : ?>
    use TranslatedTrait;

    public $langModelClass = <?= $className . $generator->langTableSuffix ?>::class;
<?php endif; ?>

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '<?= $generator->generateTableName($tableName) ?>';
    }
<?php if ($generator->db !== 'db'): ?>

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('<?= $generator->db ?>');
    }
<?php endif; ?>
<?php if (isset($behaviors) && is_array($behaviors) && !empty($behaviors)) : ?>

    /**
    * @inheritdoc
    */
    public function behaviors()
    {
        return [<?= "\n            " . implode(",\n            ", $behaviors) . ",\n        " ?>];
    }
<?php endif; ?>
<?php if ($multiLanguageModel) : ?>

    /**
     * List of all translatable attributes from
     *
     * @return array
     */
    public function getLangAttributes(): array
    {
        return [
<?php foreach ($translationAttributes as $name) : ?>
            '<?= $name; ?>',
<?php endforeach; ?>
        ];
    }
<?php endif; ?>
<?php
if (isset($relations)) {
    foreach ($relations as $name => $relation) { ?>

    /**
     * @return \yii\db\ActiveQuery
     */
    public function get<?= $name ?>()
    {
        <?= $relation[0] . "\n" ?>
    }
<?php }
} ?>
<?php foreach ($generator->imageUploaders as $uploader): ?>

    /**
    * @return \yii\db\ActiveQuery
    */
    public function <?= Generator::getRelationMethodName($uploader['attribute']) ?>()
    {
        return $this-><?= $uploader['multiple'] ? 'hasMany' : 'hasOne' ?>(EntityToFile::className(), ['entity_model_id' => 'id'])
            ->andOnCondition(['<?= $uploader['attribute'] ?>.entity_model_name' => static::formName(), '<?= $uploader['attribute'] ?>.attribute' => static::<?= Generator::getSaveAttributeConstantName($uploader['attribute']) ?>])
            ->alias('<?= $uploader['attribute'] ?>')
            ->orderBy('<?= $uploader['attribute'] ?>.position DESC');
    }
<?php endforeach; ?>
}
