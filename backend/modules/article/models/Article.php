<?php

namespace backend\modules\article\models;

use common\behaviors\SluggableBehavior;
use common\helpers\Pattern;
use common\modules\builder\widgets\BuilderForm;
use common\validators\FileRequiredValidator;
use common\validators\MultipleValidator;
use kartik\widgets\DatePicker;
use kartik\widgets\DateTimePicker;
use Yii;
use common\models\Article as CommonArticle;
use backend\components\FormBuilder;
use backend\components\BackendModel;
use backend\components\grid\TranslateColumn;
use backend\components\grid\StylingActionColumn;
use backend\modules\imagesUpload\widgets\imagesUpload\ImageUpload;
use common\models\base\EntityToFile;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%article}}".
 *
 * @property integer $id
 * @property string $label
 * @property string $alias
 * @property integer $publication_date
 * @property integer $published
 * @property integer $position
 * @property integer $created_at
 * @property integer $updated_at
 */
class Article extends CommonArticle implements BackendModel
{
    /**
     * Attribute for imageUploader
     */
    public $preview;

    /**
     * Attribute for imageUploader
     */
    public $banner;

    /**
     * Temporary sign which used for saving images before model save
     * @var string
     */
    public $sign;

    /**
     * @throws \yii\base\Exception
     */
    public function init()
    {
        parent::init();

        if (!$this->sign) {
            $this->sign = Yii::$app->getSecurity()->generateRandomString();
        }

        if (!$this->publication_date) {
            $this->publication_date = time();
        }

        $this->publication_date = date('Y-m-d', $this->publication_date);
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['label'], 'required'],
            [['label', 'alias'], 'string', 'max' => 255],
            [['alias'], 'match', 'pattern' => Pattern::alias()],
            [['alias'], 'unique'],
            [['publication_date'], 'required'],
            [['position'], 'integer', 'max' => 2147483647],
            [['published'], 'boolean'],
            [['published'], 'default', 'value' => 1],
            [['position'], 'default', 'value' => 0],
            [['sign'], 'string', 'max' => 255],
            [
                'preview',
                FileRequiredValidator::class,
                'saveAttribute' => CommonArticle::SAVE_ATTRIBUTE_PREVIEW,
                'skipOnEmpty' => false,
                'max' => 1,
            ],
            [
                'banner',
                FileRequiredValidator::class,
                'saveAttribute' => CommonArticle::SAVE_ATTRIBUTE_BANNER,
                'skipOnEmpty' => false,
                'max' => 1,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'sluggableBehavior' => [
                'class' => SluggableBehavior::class,
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('back/article', 'ID'),
            'label' => Yii::t('back/article', 'Label'),
            'alias' => Yii::t('back/article', 'Alias'),
            'publication_date' => Yii::t('back/article', 'Published date'),
            'published' => Yii::t('back/article', 'Published'),
            'position' => Yii::t('back/article', 'Position'),
            'created_at' => Yii::t('back/article', 'Created At'),
            'updated_at' => Yii::t('back/article', 'Updated At'),
            'preview' => Yii::t('back/article', 'Preview'),
            'banner' => Yii::t('back/article', 'Banner'),
        ];
    }

    /**
     * Get title for the template page
     *
     * @return string
     */
    public function getTitle()
    {
        return Yii::t('back/article', 'Article');
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
                    'label',
                    'alias',
                    'published:boolean',
                    'position',
                    [
                        'attribute' => 'publication_date',
                        'value' => function (self $model) {
                            return formatter()->asDate($model->publication_date, 'php:Y-m-d');
                        },
                        'filter' => false,
                    ],
                    ['class' => TranslateColumn::class],
                    ['class' => StylingActionColumn::class],
                ];
                break;
            case 'view':
                return [
                    'id',
                    'label',
                    'alias',
                    [
                        'attribute' => 'publication_date',
                        'value' => function (self $model) {
                            return formatter()->asDate($model->publication_date, 'php:Y-m-d');
                        },
                        'filter' => false,
                    ],
                    'published:boolean',
                    'position',
                ];
                break;
        }

        return [];
    }

    /**
     * @return ArticleSearch
     */
    public function getSearchModel()
    {
        return new ArticleSearch();
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getFormConfig()
    {
        return [
            Yii::t('back/app', 'Main') => [
                'label' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'alias' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'publication_date' => [
                    'type' => FormBuilder::INPUT_WIDGET,
                    'widgetClass' => DatePicker::class,
                    'options' => [
                        'options' => [
                            'class' => 'form-control',
                        ],
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'todayHighlight' => true,
                            'todayBtn' => true,
                            'autoclose' => true,

                        ]
                    ],
                ],
                'preview' => [
                    'type' => FormBuilder::INPUT_RAW,
                    'value' => ImageUpload::widget([
                        'model' => $this,
                        'attribute' => 'preview',
                        'saveAttribute' => CommonArticle::SAVE_ATTRIBUTE_PREVIEW,
                        'aspectRatio' => false,
                        'multiple' => false,
                        'allowedFileExtensions' => IMAGE_VALID_FORMATS,
                        'maxFileSize' => MAX_IMAGE_KB
                    ])
                ],
                'banner' => [
                    'type' => FormBuilder::INPUT_RAW,
                    'value' => ImageUpload::widget([
                        'model' => $this,
                        'attribute' => 'banner',
                        'saveAttribute' => CommonArticle::SAVE_ATTRIBUTE_BANNER,
                        'aspectRatio' => false,
                        'multiple' => false,
                        'allowedFileExtensions' => IMAGE_VALID_FORMATS,
                        'maxFileSize' => MAX_IMAGE_KB
                    ])
                ],
                'published' => [
                    'type' => FormBuilder::INPUT_CHECKBOX,
                ],
                'position' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'sign' => [
                    'type' => FormBuilder::INPUT_HIDDEN,
                    'label' => false,
                ],
            ],

            Yii::t('back/app', 'Constructor') => [
                'builderContent' => [
                    'type' => FormBuilder::INPUT_WIDGET,
                    'widgetClass' => BuilderForm::class,
                    'label' => false,
                    'options' => [
                        'model' => $this,
                        'attribute' => 'builderContent',
                    ]
                ],
            ],
        ];
    }

    public function afterFind()
    {
        parent::afterFind();

        if (!$this->publication_date) {
            $this->publication_date = time();
        }

        $this->publication_date = date('Y-m-d', $this->publication_date);
    }

    /**
     * @param bool $insert
     *
     * @return bool
     */
    public function beforeSave($insert)
    {
        $this->publication_date = strtotime($this->publication_date);

        return parent::beforeSave($insert);
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        EntityToFile::updateImages($this->id, $this->sign);
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function afterDelete()
    {
        parent::afterDelete();

        EntityToFile::deleteImages($this->formName(), $this->id);
    }
}
