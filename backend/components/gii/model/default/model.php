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

echo "<?php\n";
?>

namespace <?= $generator->ns ?>;

use Yii;

/**
 * @inheritdoc
<?php if (!empty($relations)): ?>
 *
<?php foreach ($relations as $name => $relation): ?>
 * @property <?= $relation[1] . ($relation[2] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?>
<?php endforeach; ?>
<?php endif; ?>
 */
class <?= $className ?> extends <?= '\\' . ltrim($generator->ns, '\\') . '\\base\\' . $className ?><?= $multiLanguageModel ? ' implements \common\components\model\Translateable' : null ?><?= "\n" ?>
{
<?php if ($multiLanguageModel) : ?>
    use \backend\components\TranslateableTrait;

    /**
     * @return array
     */
    public static function getTranslationAttributes()
    {
        return [
<?php foreach ($translationAttributes as $attribute): ?>
            <?= "'$attribute',\n" ?>
<?php endforeach; ?>
        ];
    }

<?php endif; ?>
<?php if (is_array($behaviors) && !empty($behaviors)) : ?>
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return \yii\helpers\ArrayHelper::merge(parent::behaviors(), [
            <?= implode(",\n            ", $behaviors) . ",\n" ?>
        ]);
    }

<?php endif; ?>
<?php foreach ($relations as $name => $relation): ?>
    /**
     * @return \yii\db\ActiveQuery
     */
    public function get<?= $name ?>()
    {
        <?= $relation[0] . "\n" ?>
    }

<?php endforeach; ?>
}
