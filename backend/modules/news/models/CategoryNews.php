<?php

namespace backend\modules\news\models;

use Yii;
use common\models\CategoryNews as CommonCategoryNews;
use backend\components\FormBuilder;
use backend\components\BackendModel;
use backend\components\grid\TranslateColumn;
use backend\components\grid\StylingActionColumn;
use yii\helpers\ArrayHelper;
use common\behaviors\SluggableBehavior;
use common\helpers\Pattern;


/**
 * This is the model class for table "{{%category_news}}".
 *
 * @property integer $id
 * @property string $alias
 * @property integer $published
 * @property integer $position
 * @property integer $created_at
 * @property integer $updated_at
 */
class CategoryNews extends CommonCategoryNews implements BackendModel
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
            ]
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
        return Yii::t('back/base', 'Category News');
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
                ];
            break;
        }

        return [];
    }

    /**
    * @return CategoryNewsSearch
    */
    public function getSearchModel()
    {
        return new CategoryNewsSearch();
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
        ];
    }
}
