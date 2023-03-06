<?php

namespace backend\modules\menu\models;

use backend\modules\page\models\Page;
use Yii;
use common\modules\menu\models\Menu as CommonMenu;
use backend\components\FormBuilder;
use backend\components\BackendModel;
use backend\components\grid\TranslateColumn;
use backend\components\grid\StylingActionColumn;
use yii\helpers\Html;

/**
 * This is the model class for table "{{%menu}}".
 *
 * @property integer $id
 * @property integer $location
 * @property string $link
 * @property integer $published
 * @property integer $position
 * @property integer $created_at
 * @property integer $updated_at
 */
class Menu extends CommonMenu implements BackendModel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['label', 'location'], 'required'],

            [['label'], 'string', 'max' => 255],

            ['page_id', 'integer'],
            ['page_id', 'exist', 'targetClass' => Page::class, 'targetAttribute' => 'id'],

            [['link'], 'string', 'max' => 255],
            [['link'], 'url', 'defaultScheme' => 'http'],

            [['location'], 'integer'],
            [['location'], 'default', 'value' => 0],

            [['published'], 'boolean'],
            [['published'], 'default', 'value' => 1],

            [['position'], 'integer', 'min' => -2147483647, 'max' => 2147483647],
            [['position'], 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('back/menu-module', 'ID'),
            'location' => Yii::t('back/menu-module', 'Location'),
            'page_id' => Yii::t('back/menu-module', 'Page'),
            'link' => Yii::t('back/menu-module', 'Link'),
            'published' => Yii::t('back/menu-module', 'Published'),
            'position' => Yii::t('back/menu-module', 'Position'),
            'created_at' => Yii::t('back/menu-module', 'Created At'),
            'updated_at' => Yii::t('back/menu-module', 'Updated At'),
            'label' => Yii::t('back/menu-module', 'Label'),
        ];
    }

    /**
     * Get title for the template page
     *
     * @return string
     */
    public function getTitle()
    {
        return Yii::t('back/menu-module', 'Menu');
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
                        'attribute' => 'location',
                        'filter' => self::getLocationsList(),
                        'value' => function (self $model) {
                            return $model->getLocation();
                        }
                    ],
                    [
                        'attribute' => 'page_id',
                        'filter' => Page::getListItems(),
                        'value' => function (self $model) {
                            return $model->page->label ?? null;
                        }
                    ],
                    'label',
                    [
                        'attribute' => 'link',
                        'format' => 'raw',
                        'value' => function (self $model) {
                            $domain = configurator()->get('app.front.domain');
                            return Html::a($model->link, $domain . $model->link, ['target' => '_blank']);
                        }
                    ],
                    'published:boolean',
                    'position',
                    ['class' => TranslateColumn::class],
                    ['class' => StylingActionColumn::class],
                ];
                break;
            case 'view':
                return [];
                break;
        }

        return [];
    }

    /**
     * @return MenuSearch
     */
    public function getSearchModel()
    {
        return new MenuSearch();
    }

    /**
     * @return array
     */
    public function getFormConfig()
    {
        return [
            Yii::t('back/app', 'Main') => [
                'label' => ['type' => FormBuilder::INPUT_TEXT],
                'location' => [
                    'type' => FormBuilder::INPUT_DROPDOWN_LIST,
                    'items' => self::getLocationsList(),
                    'options' => ['prompt' => '---'],
                ],
                'page_id' => [
                    'type' => FormBuilder::INPUT_DROPDOWN_LIST,
                    'items' => Page::getListItems(),
                    'options' => ['prompt' => '---'],
                ],
                'link' => ['type' => FormBuilder::INPUT_TEXT],
                'published' => ['type' => FormBuilder::INPUT_CHECKBOX],
                'position' => ['type' => FormBuilder::INPUT_TEXT],
            ],
        ];
    }
}
