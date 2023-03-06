<?php

namespace backend\modules\seo\models;

use Yii;
use common\modules\seo\models\MetaTags as CommonMetaTags;
use backend\components\FormBuilder;
use backend\components\BackendModel;
use backend\components\grid\StylingActionColumn;


/**
 * This is the model class for table "{{%meta_tags}}".
 *
 * @property integer $id
 * @property string $label
 * @property string $name
 * @property integer $type
 * @property integer $position
 */
class MetaTags extends CommonMetaTags implements BackendModel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'label'], 'required'],
            [['type', 'position'], 'integer'],
            [['name', 'label'], 'string', 'max' => 255],
            [['name'],'unique'],
            [['position'], 'default', 'value' => 0],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('back/seo-tags', 'ID'),
            'label' => Yii::t('back/seo-tags', 'Label'),
            'name' => Yii::t('back/seo-tags', 'Name'),
            'type' => Yii::t('back/seo-tags', 'Type'),
            'position' => Yii::t('back/seo-tags', 'Position'),
        ];
    }

    /**
    * Get title for the template page
    *
    * @return string
    */
    public function getTitle()
    {
        return Yii::t('back/seo-tags', 'Meta Tags');
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
                    'name',
                    [
                        'attribute' => 'type',
                        'filter' => self::getTypesList(),
                        'value' => function (self $model) {
                            return $model::getTypesList()[$model->type] ?? null;
                        }
                    ],
                    'position',
                    ['class' => StylingActionColumn::class],
                ];
            break;
            case 'view':
                return [
                    'id',
                    'name',
                    'type',
                    'position',
                ];
            break;
        }

        return [];
    }

    /**
    * @return MetaTagsSearch
    */
    public function getSearchModel()
    {
        return new MetaTagsSearch();
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
                'name' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'type' => [
                    'type' => FormBuilder::INPUT_DROPDOWN_LIST,
                    'items' => self::getTypesList()
                ],
                'position' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
            ],
        ];
    }

    public static function getTypesList(): array
    {
        return [
            self::TYPE_TEXT => bt('Text', 'seo-tags'),
            self::TYPE_TEXTAREA => bt('Text area', 'seo-tags'),
            self::TYPE_IMAGE => bt('Image', 'seo-tags'),
            self::TYPE_CHECKBOX => bt('Checkbox', 'seo-tags'),
            self::TYPE_CODE => bt('Code', 'seo-tags'),
        ];
    }
}
