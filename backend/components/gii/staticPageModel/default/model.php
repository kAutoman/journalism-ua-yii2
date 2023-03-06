<?php
/**
 * This is the template for generating the model class of a specified table.
 */

use yii\helpers\Inflector;

/* @var $this yii\web\View */
/* @var $generator \backend\components\gii\staticPageModel\Generator */
/* @var $behaviors string[] list of behaviors */
/* @var $hasFiles bool */
//var_dump($generator->hasFiles);die;
echo "<?php\n";
$modelClassName = $generator->modelClassName;
?>

namespace <?= $generator->ns ?>;

use Yii;
use common\models\<?= $modelClassName ?> as Common<?= $modelClassName ?>;
use backend\components\FormBuilder;
<?php if ($hasFiles) : ?>
use backend\modules\imagesUpload\widgets\imagesUpload\ImageUpload;
<?php endif; ?>

/**
 * Class <?= $modelClassName . "\n"; ?>
 *
 * @package <?= $generator->ns . "\n"; ?>
 */
class <?= $modelClassName ?> extends Common<?= $modelClassName . "\n" ?>
{

    /**
     * Title of the form
     *
     * @return string
     */
    public function getTitle()
    {
        return <?= $generator->generateString($generator->title) ?>;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            <?= implode(",\n            ", $generator->generateRules()) . "\n"; ?>
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
<?php foreach ($generator->keys as $key):
        if ($constant = $generator->getConstantName($key['type'])): ?>
            '<?= $generator->camelCase($key['id']); ?>' => Yii::t('<?= $generator->messageCategory; ?>', '<?= Inflector::camel2words($key['id']); ?>'),
<?php endif;
    endforeach; ?>
        ];
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return [
<?php foreach ($generator->keys as $key): ?>
            '<?= $generator->camelCase($key['id']); ?>',
<?php endforeach; ?>
        ];
    }
<?php if ($behaviors) : ?>

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

    /**
     * @return array
<?php if ($hasFiles) : ?>
     * @throws \Exception
<?php endif; ?>
     */
    public function getFormConfig()
    {
        $config = [
            Yii::t('back/app', 'Main') => [
<?php foreach ($generator->keys as $key): ?>
                '<?= $generator->camelCase($key['id']) ?>' => <?= $generator->generateFormFieldConfig($key); ?>,
<?php endforeach; ?>
            ],
<?php foreach ($generator->relationsForRelatedFormWidget as $relation): ?>
            '<?= $relation['tabName'] ?>' => [
                $this->getRelatedFormConfig()['<?= $relation['relationName'] ?>']
            ],
<?php endforeach; ?>
        ];

        return $config;
    }
<?php if ($generator->relationsForRelatedFormWidget): ?>

    /**
    * @return array
    */
    public function getRelatedFormConfig()
    {
        $config = [
<?php foreach ($generator->relationsForRelatedFormWidget as $relation): ?>
            '<?= $relation['relationName'] ?>' => [
                'relation' => '<?= $relation['relationName'] ?>',
            ],
<?php endforeach; ?>
        ];

        return $config;
    }

    /**
    * @return ActiveQueryInterface
    */
    public function getRelationName()
    {
        return $this->hasMany(ModelName::className(), ['foreign_key' => 'id'])->orderBy('position');
    }
<?php endif; ?>
}
