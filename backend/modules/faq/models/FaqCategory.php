<?php

namespace backend\modules\faq\models;

use Yii;
use common\modules\faq\models\FaqCategory as CommonFaqCategory;
use backend\components\FormBuilder;
use backend\components\BackendModel;
use backend\components\grid\TranslateColumn;
use backend\components\grid\StylingActionColumn;
use yii\helpers\ArrayHelper;
use common\behaviors\SluggableBehavior;
use common\helpers\Pattern;


/**
 * This is the model class for table "{{%faq_category}}".
 *
 * @property integer $id
 * @property string $alias
 * @property integer $published
 * @property integer $position
 * @property integer $created_at
 * @property integer $updated_at
 */
class FaqCategory extends CommonFaqCategory implements BackendModel
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['published'], 'boolean'],
            [['position'], 'integer'],
            [['label'], 'required'],
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
            ]
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('back/faq-category', 'ID'),
            'alias' => Yii::t('back/faq-category', 'Alias'),
            'published' => Yii::t('back/faq-category', 'Published'),
            'position' => Yii::t('back/faq-category', 'Position'),
            'created_at' => Yii::t('back/faq-category', 'Created At'),
            'updated_at' => Yii::t('back/faq-category', 'Updated At'),
            'label' => Yii::t('back/faq-category', 'Label'),
        ];
    }

    /**
    * Get title for the template page
    *
    * @return string
    */
    public function getTitle()
    {
        return Yii::t('back/faq-category', 'Faq Category');
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
                    ['class' => TranslateColumn::class],
                    [
                        'class' => StylingActionColumn::class,
                        'visibleButtons' => [
                            'delete' => function (self $model) {
                                return !$model->isPermanent() && empty($model->faqs);
                            },
                        ],
                    ],
                ];
            break;
            case 'view':
                return [
                    'label',
                    'id',
                    'alias',
                    'published:boolean',
                    'position',
                ];
            break;
        }

        return [];
    }

    /**
    * @return FaqCategorySearch
    */
    public function getSearchModel()
    {
        return new FaqCategorySearch();
    }

    /**
    * @return array
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
                'published' => [
                    'type' => FormBuilder::INPUT_CHECKBOX,
                ],
                'position' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
            ],
        ];
    }
}
