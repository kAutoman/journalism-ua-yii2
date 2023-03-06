<?php

namespace backend\modules\faq\models;

use backend\widgets\Editor;
use Exception;
use Yii;
use common\modules\faq\models\Faq as CommonFaq;
use backend\components\FormBuilder;
use backend\components\BackendModel;
use backend\components\grid\TranslateColumn;
use backend\components\grid\StylingActionColumn;


/**
 * This is the model class for table "{{%faq}}".
 *
 * @property integer $id
 * @property integer $category_id
 * @property integer $published
 * @property integer $position
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property FaqCategory $category
 */
class Faq extends CommonFaq implements BackendModel
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'question', 'answer'], 'required'],
            [['category_id', 'position'], 'integer'],
            [['published'], 'boolean'],
            [['answer'], 'string'],
            [['question'], 'string', 'max' => 255],
            [['category_id'], 'exist', 'targetClass' => FaqCategory::class, 'targetAttribute' => 'id'],
            [['published'], 'default', 'value' => 1],
            [['position'], 'default', 'value' => 0],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('back/faq', 'ID'),
            'category_id' => Yii::t('back/faq', 'Category'),
            'published' => Yii::t('back/faq', 'Published'),
            'position' => Yii::t('back/faq', 'Position'),
            'created_at' => Yii::t('back/faq', 'Created At'),
            'updated_at' => Yii::t('back/faq', 'Updated At'),
            'question' => Yii::t('back/faq', 'Question'),
            'answer' => Yii::t('back/faq', 'Answer'),
        ];
    }

    /**
    * Get title for the template page
    *
    * @return string
    */
    public function getTitle()
    {
        return Yii::t('back/faq', 'Faq');
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
                    [
                        'attribute' => 'category_id',
                        'value' => 'category.label',
                        'filter' => FaqCategory::getList()
                    ],
                    'question',
                    'published:boolean',
                    'position',
                    ['class' => TranslateColumn::class],
                    ['class' => StylingActionColumn::class],
                ];
            break;
            case 'view':
                return [
                    'question',
                    'answer',
                    'id',
                    'category_id',
                    'published:boolean',
                    'position',
                ];
            break;
        }

        return [];
    }

    /**
    * @return FaqSearch
    */
    public function getSearchModel()
    {
        return new FaqSearch();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getFormConfig()
    {
        return [
            Yii::t('back/app', 'Main') => [
                'category_id' => [
                    'type' => FormBuilder::INPUT_DROPDOWN_LIST,
                    'items' => FaqCategory::getList(),
                ],
                'question' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'answer' => [
                    'type' => FormBuilder::INPUT_RAW,
                    'value' => Editor::widget([
                        'model' => $this,
                        'attribute' => 'answer',
                    ])
                ],
                'published' => ['type' => FormBuilder::INPUT_CHECKBOX],
                'position' => ['type' => FormBuilder::INPUT_TEXT],
            ],
        ];
    }
}
