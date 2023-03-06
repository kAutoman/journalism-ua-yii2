<?php

namespace backend\modules\news\models;

use common\behaviors\ManyToManyBehavior;
use common\models\NewsToCategory;
use common\modules\builder\widgets\BuilderForm;
use common\widgets\SelectizeDropownWidget;
use dosamigos\selectize\SelectizeDropDownList;
use kartik\widgets\DateTimePicker;
use Yii;
use common\models\News as CommonNews;
use backend\components\FormBuilder;
use backend\components\BackendModel;
use backend\components\grid\TranslateColumn;
use backend\components\grid\StylingActionColumn;
use yii\helpers\ArrayHelper;
use common\behaviors\SluggableBehavior;
use common\helpers\Pattern;


/**
 * This is the model class for table "{{%news}}".
 *
 * @property integer $id
 * @property string $alias
 * @property integer $published
 * @property integer $position
 * @property integer $publication_date
 * @property integer $created_at
 * @property integer $updated_at
 */
class News extends CommonNews implements BackendModel
{
    public $categories;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['published'], 'boolean'],
            [['position'], 'integer'],
            [['categories'], 'safe'],
            [
                ['publication_date'],
                'filter',
                'filter' => function ($value) {
                    return is_int($value) ? $value : strtotime($value);
                }
            ],
            [['label'], 'required'],
            [['content'], 'string'],
            [['alias', 'label'], 'string', 'max' => 255],
            [['alias'], 'match', 'pattern' => Pattern::alias()],
            [['published'], 'default', 'value' => 1],
            [['position'], 'default', 'value' => 0],
            [['alias'], 'unique'],
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
            'categories' => [
                'class' => ManyToManyBehavior::class,
                'modelClass' => NewsToCategory::class,
                'ownerField' => 'news_id',
                'relatedField' => 'category_id',
                'fieldName' => 'categories'
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('back/base', 'ID'),
            'alias' => Yii::t('back/base', 'Alias'),
            'published' => Yii::t('back/base', 'Published'),
            'position' => Yii::t('back/base', 'Position'),
            'publication_date' => Yii::t('back/base', 'Publication date'),
            'created_at' => Yii::t('back/base', 'Created At'),
            'updated_at' => Yii::t('back/base', 'Updated At'),
            'label' => Yii::t('back/base', 'Label'),
            'content' => Yii::t('back/base', 'Content'),
        ];
    }

    /**
     * Get title for the template page
     *
     * @return string
     */
    public function getTitle()
    {
        return Yii::t('back/base', 'News');
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
                    'publication_date',
                    ['class' => TranslateColumn::class],
                    ['class' => StylingActionColumn::class],
                ];
                break;
            case 'view':
                return [
                    'label',
                    'content',
                    'id',
                    'alias',
                    'published:boolean',
                    'position',
                    'publication_date',
                ];
                break;
        }

        return [];
    }

    /**
     * @return NewsSearch
     */
    public function getSearchModel()
    {
        return new NewsSearch();
    }

    /**
     * @return array
     */
    public function getFormConfig()
    {
        $this->publication_date = Yii::$app->formatter->asDatetime($this->publication_date, 'php:Y-m-d H:i:s');
        $this->categories = array_keys($this->categoriesIds);

        return [
            Yii::t('back/app', 'Main') => [
                'label' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'publication_date' => [
                    'type' => FormBuilder::INPUT_WIDGET,
                    'widgetClass' => DateTimePicker::class,
                    'options' => [
                        'options' => [
                            'class' => 'form-control',
                        ],
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd hh:ii:ss',
                            'todayHighlight' => true,
                            'todayBtn' => true,
                            'autoclose' => true,

                        ]
                    ],
                ],
                'categories' => [
                    'type' => FormBuilder::INPUT_WIDGET,
                    'widgetClass' => SelectizeDropownWidget::class,
                    'options' => [
                        'model' => $this,
                        'attribute' => 'categories',
                        'value' => $this->categories,
                        'items' => CategoryNews::getListItems(),
                        'options' => [
                            'multiple' => true,
                        ],
                        'clientOptions' => [
                            'plugins' => ['remove_button', 'drag_drop'],
                        ],

                    ]

                ],
                'content' => [
                    'type' => FormBuilder::INPUT_TEXTAREA,
                ],
                'alias' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'published' => [
                    'type' => FormBuilder::INPUT_CHECKBOX,
                ],
                'position' => [
                    'type' => FormBuilder::INPUT_TEXT,
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
}
