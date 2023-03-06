<?php

namespace backend\modules\log\models;

use common\models\User;
use Yii;
use common\models\UserLog as CommonUserLog;
use backend\components\FormBuilder;
use backend\components\BackendModel;
use backend\components\grid\StylingActionColumn;
use yii\helpers\Html;
use yii\helpers\Json;


/**
 * This is the model class for table "{{%user_log}}".
 *
 * @property integer $id
 * @property string $action
 * @property integer $user_id
 * @property string $model_class
 * @property string $entity_id
 * @property string $content_before
 * @property string $user_info
 * @property string $content_after
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $user
 */
class UserLog extends CommonUserLog implements BackendModel
{


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('back/base', 'ID'),
            'action' => Yii::t('back/base', 'Action'),
            'user_id' => Yii::t('back/base', 'User'),
            'model_class' => Yii::t('back/base', 'Model class'),
            'entity_id' => Yii::t('back/base', 'Entity id'),
            'content_before' => Yii::t('back/base', 'Content before'),
            'user_info' => Yii::t('back/base', 'User info'),
            'content_after' => Yii::t('back/base', 'Content after'),
            'created_at' => Yii::t('back/base', 'Created At'),
            'updated_at' => Yii::t('back/base', 'Updated At'),
        ];
    }

    /**
     * Get title for the template page
     *
     * @return string
     */
    public function getTitle()
    {
        return Yii::t('back/base', 'User Log');
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
        $users = User::find()->select('username')->indexBy('id')->column();
        switch ($page) {
            case 'index':
                return [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute'=>'action',
                        'filter'=>[self::ACTION_CREATE => self::ACTION_CREATE, self::ACTION_UPDATE=>self::ACTION_UPDATE, self::ACTION_DELETE=>self::ACTION_DELETE]
                    ],

                    [
                        'attribute'=>'user_id',
                        'value'=>function(self $model) {
                            return $model->user->username ?? null;
                        },
                        'filter'=>$users
                    ],
                    'created_at:datetime',
                    [
                        'attribute' => 'model_class',
                        'format' => 'html',
                        'value' => function (self $model) {

                            return Html::tag('div', $model->model_class, ['class'=>'grid-max-150']);
                        }
                    ],
                    'entity_id',

                    [
                        'attribute' => 'content_before',
                        'format' => 'html',
                        'value' => function (self $model) {
                            if ($model->content_before === null) {
                                return null;
                            }


                            $content = Json::encode(Json::decode($model->content_before, false),
                                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

                            return Html::tag('pre', Html::encode($content), ['class' => 'pre-import-info']);
                        }
                    ],
                    [
                        'attribute' => 'content_after',
                        'format' => 'html',
                        'value' => function (self $model) {
                            if ($model->content_after === null) {
                                return null;
                            }
                            $content = Json::encode(Json::decode($model->content_after, false),
                                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

                            return Html::tag('pre', Html::encode($content), ['class' => 'pre-import-info']);
                        }
                    ],
                    ['class' => StylingActionColumn::class, 'template'=>'{view}'],
                ];
                break;
            case 'view':
                return [
                    'id',
                    'action',
                    [
                        'attribute'=>'user_id',
                        'value'=>function(self $model) {
                            return $model->user->username ?? null;
                        },
                    ],
                    'created_at:datetime',

                    'model_class',
                    'entity_id',

                    [
                        'attribute' => 'content_before',
                        'format' => 'html',
                        'value' => function (self $model) {
                            if ($model->content_before === null) {
                                return null;
                            }


                            $content = Json::encode(Json::decode($model->content_before, false),
                                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

                            return Html::tag('pre', Html::encode($content), ['class' => 'pre-import-info']);
                        }
                    ],
                    [
                        'attribute' => 'content_after',
                        'format' => 'html',
                        'value' => function (self $model) {
                            if ($model->content_after === null) {
                                return null;
                            }
                            $content = Json::encode(Json::decode($model->content_after, false),
                                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

                            return Html::tag('pre', Html::encode($content), ['class' => 'pre-import-info']);
                        }
                    ],
                    [
                        'attribute' => 'user_info',
                        'format' => 'html',
                        'value' => function (self $model) {
                            if ($model->user_info === null) {
                                return null;
                            }
                            $content = Json::encode(Json::decode($model->user_info, false),
                                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

                            return Html::tag('pre', Html::encode($content), ['class' => 'pre-import-info']);
                        }
                    ],
                ];
                break;
        }

        return [];
    }

    /**
     * @return UserLogSearch
     */
    public function getSearchModel()
    {
        return new UserLogSearch();
    }

    /**
     * @return array
     */
    public function getFormConfig()
    {
        return [
            Yii::t('back/app', 'Main') => [
                'action' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'user_id' => [
                    'type' => FormBuilder::INPUT_DROPDOWN_LIST,
                    'items' => \common\models\User::getListItems(),
                    'options' => [
                        'prompt' => '',
                    ],
                ],
                'model_class' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'entity_id' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'content_before' => [
                    'type' => FormBuilder::INPUT_TEXTAREA
                ],
                'user_info' => [
                    'type' => FormBuilder::INPUT_TEXTAREA
                ],
                'content_after' => [
                    'type' => FormBuilder::INPUT_TEXTAREA
                ],
            ],
        ];
    }
}
