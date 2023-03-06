<?php

namespace backend\modules\member\models;

use Yii;
use common\models\MemberTimeline as CommonMemberTimeline;
use backend\components\FormBuilder;
use backend\components\BackendModel;
use backend\components\grid\TranslateColumn;
use backend\components\grid\StylingActionColumn;

/**
 * This is the model class for table "{{%member_timeline}}".
 *
 * @property integer $id
 * @property integer $published
 * @property integer $position
 * @property integer $created_at
 * @property integer $updated_at
 */
class MemberTimeline extends CommonMemberTimeline implements BackendModel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['label'], 'required'],
            [['content'], 'string'],
            [['label', 'date'], 'string', 'max' => 255],

            [['published'], 'boolean'],
            [['published'], 'default', 'value' => 1],

            [['position'], 'integer'],
            [['position'], 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('back/member', 'ID'),
            'published' => Yii::t('back/member', 'Published'),
            'position' => Yii::t('back/member', 'Position'),
            'created_at' => Yii::t('back/member', 'Created At'),
            'updated_at' => Yii::t('back/member', 'Updated At'),
            'label' => Yii::t('back/member', 'Label'),
            'content' => Yii::t('back/member', 'Content'),
            'date' => Yii::t('back/member', 'Date'),
        ];
    }

    /**
     * Get title for the template page
     *
     * @return string
     */
    public function getTitle()
    {
        return Yii::t('back/member', 'Member Timeline');
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
                    'date',
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
                    'date',
                    'id',
                    'published:boolean',
                    'position',
                ];
                break;
        }

        return [];
    }

    /**
     * @return MemberTimelineSearch
     */
    public function getSearchModel()
    {
        return new MemberTimelineSearch();
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
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'date' => [
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
