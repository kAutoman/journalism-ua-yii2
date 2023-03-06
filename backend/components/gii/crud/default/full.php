<?php
/**
 * This is the template for generating the model class of a specified table.
 */

use backend\components\gii\migration\Generator;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator backend\components\gii\crud\Generator|Generator */
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
/* @var $hasSluggable bool */

$commonClassName = "Common{$className}";

echo "<?php\n";
?>

namespace <?= $generator->ns ?>;

use Yii;
use common\models\<?= $className; ?> as <?= $commonClassName; ?>;
use backend\components\FormBuilder;
use backend\components\BackendModel;
<?php if ($multiLanguageModel) : ?>
use backend\components\grid\TranslateColumn;
<?php endif; ?>
use backend\components\grid\StylingActionColumn;
<?php if ($generator->imageUploaders) : ?>
use backend\modules\imagesUpload\widgets\imagesUpload\ImageUpload;
use common\models\base\EntityToFile;
<?php endif; ?>
<?php if ($hasSluggable) : ?>
use yii\helpers\ArrayHelper;
use common\behaviors\SluggableBehavior;
use common\helpers\Pattern;
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
 */
class <?= $className ?> extends <?= $commonClassName; ?> implements BackendModel
{
<?php if ($generator->imageUploaders) : ?>
<?php foreach ($generator->imageUploaders as $uploader): ?>
    /**
    * Attribute for imageUploader
    */
    public $<?= $uploader['attribute'] ?>;

<?php endforeach; ?>
    /**
     * Temporary sign which used for saving images before model save
     * @var string
     */
    public $sign;

    public function init()
    {
        parent::init();

        if (!$this->sign) {
            $this->sign = Yii::$app->getSecurity()->generateRandomString();
        }
    }
    
<?php endif; ?>
<?php if ($generator->db !== 'db'): ?>

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('<?= $generator->db ?>');
    }
<?php endif; ?>

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [<?= "\n            " . implode(",\n            ", $rules) . ",\n        " ?>
<?php if ($generator->imageUploaders): ?>
    [['sign'], 'string', 'max' => 255],<?= "\n         " ?>
<?php endif; ?>
];
    }
<?php if ($hasSluggable) : ?>

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'sluggableBehavior' => [
                'class' => SluggableBehavior::class,
            ]
        ]);
    }

<?php endif; ?>
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
<?php foreach ($labels as $name => $label): ?>
            <?= "'$name' => " . $generator->generateStringWithTable($tableSchema, $label) . ",\n" ?>
<?php endforeach; ?>
<?php foreach ($generator->imageUploaders as $uploader): ?>
            <?= "'{$uploader['attribute']}' => " . $generator->generateString($uploader['attributeLabel']) . ",\n" ?>
<?php endforeach; ?>
        ];
    }

    /**
    * Get title for the template page
    *
    * @return string
    */
    public function getTitle()
    {
        return Yii::t('<?= $generator->messageCategory; ?>', '<?= Inflector::camel2words(StringHelper::basename($generator->modelClass)) ?>');
    }

    /**
    * Get attribute columns for index and view page
    *
    * @param $page
    *
    * @return array
    */
    public function getColumns($page)
    {
        switch ($page) {
            case 'index':
                return [
                    ['class' => 'yii\grid\SerialColumn'],
<?php foreach ($indexColumns as $column) : ?>
<?php if (is_array($column)) : ?>
                    [
<?php foreach ($column as $key => $name) : ?>
                        '<?= $key; ?>' => '<?= $name; ?>',
<?php endforeach; ?>
                    ],
<?php else : ?>
                    <?= "'" . $column . "',\n"; ?>
<?php endif; ?>
<?php endforeach; ?>
<?php if ($multiLanguageModel) : ?>
                    ['class' => TranslateColumn::class],
<?php endif; ?>
                    ['class' => StylingActionColumn::class],
                ];
            break;
            case 'view':
                return [
                    <?= implode(",\n                    ", $viewColumns) . ",\n" ?>
                ];
            break;
        }

        return [];
    }

    /**
    * @return <?= StringHelper::basename($generator->modelClass) ?>Search
    */
    public function getSearchModel()
    {
        return new <?= StringHelper::basename($generator->modelClass) ?>Search();
    }

    /**
    * @return array
    */
    public function getFormConfig()
    {
        return [
            Yii::t('back/app', 'Main') => [
<?php foreach ($formColumns as $attribute => $config) : ?>
                '<?= $attribute ?>' => <?= $config ?>,
<?php endforeach; ?>
<?php if ($generator->imageUploaders) : ?>
    <?php foreach ($generator->imageUploaders as $uploader): ?>
            '<?= $uploader['attribute'] ?>' => [
                    'type' => FormBuilder::INPUT_RAW,
                    'value' => ImageUpload::widget([
                        'model' => $this,
                        'attribute' => '<?= $uploader['attribute'] ?>',
                        'saveAttribute' => <?= $commonClassName ?>::<?= Generator::getSaveAttributeConstantName($uploader['attribute']) ?>,
                        'aspectRatio' => <?= $uploader['aspectRatio'] ?: 'false' ?>,
                        'multiple' => <?= $uploader['multiple'] ? 'true' : 'false' ?>,
                    ])
                ],
    <?php endforeach; ?>
            'sign' => [
                    'type' => FormBuilder::INPUT_HIDDEN,
                    'label' => false,
                ],
<?php endif; ?>
            ],
        ];
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
                'relation' => '<?= $relation['relationName'] ?>'
            ],
<?php endforeach; ?>
        ];

        return $config;
    }
<?php endif; ?>
<?php if ($generator->imageUploaders) { ?>
    /**
    * @inheritdoc
    */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        EntityToFile::updateImages($this->id, $this->sign);
    }

    /**
    * @inheritdoc
    */
    public function afterDelete()
    {
        parent::afterDelete();

        EntityToFile::deleteImages($this->formName(), $this->id);
    }
<?php } ?>
}
