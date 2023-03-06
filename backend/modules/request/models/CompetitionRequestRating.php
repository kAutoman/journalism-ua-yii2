<?php

namespace backend\modules\request\models;

use common\models\User;
use Yii;
use common\models\CompetitionRequestRating as CommonCompetitionRequestRating;
use backend\components\FormBuilder;
use backend\components\BackendModel;
use backend\components\grid\StylingActionColumn;

/**
 * Class CompetitionRequestRating
 * @package backend\modules\request\models
 */
class CompetitionRequestRating extends CommonCompetitionRequestRating implements BackendModel
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['request_id', 'user_id', 'rating'], 'integer'],
            [
                ['request_id'],
                'exist',
                'targetClass' => CompetitionRequest::class,
                'targetAttribute' => 'id'
            ],
            [
                ['user_id'],
                'exist',
                'targetClass' => User::class,
                'targetAttribute' => 'id'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('back/rating', 'ID'),
            'request_id' => Yii::t('back/rating', 'Request ID'),
            'user_id' => Yii::t('back/rating', 'User ID'),
            'rating' => Yii::t('back/rating', 'Rating'),
            'created_at' => Yii::t('back/rating', 'Created At'),
            'updated_at' => Yii::t('back/rating', 'Updated At'),
        ];
    }

    /**
     * Get title for the template page
     *
     * @return string
     */
    public function getTitle()
    {
        return Yii::t('back/rating', 'Competition Request Rating');
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
                        'attribute' => 'request_id',
                        'value' => function (self $model) {
                            return $model->request->name ?? null;
                        },
                        'filter' => CompetitionRequest::getListItems('id', 'name'),
                    ],
                    [
                        'attribute' => 'user_id',
                        'value' => function (self $model) {
                            return $model->user->email ?? null;
                        },
                        'filter' => User::getListItems('id', 'email'),
                    ],
                    'rating',
                    ['class' => StylingActionColumn::class],
                ];
                break;
            case 'view':
                return [
                    'id',
                    [
                        'attribute' => 'request_id',
                        'value' => $this->request->email ?? null,
                    ],
                    [
                        'attribute' => 'user_id',
                        'value' => $this->user->email ?? null,
                    ],
                    'rating',
                ];
                break;
        }

        return [];
    }

    /**
     * @return CompetitionRequestRatingSearch
     */
    public function getSearchModel()
    {
        return new CompetitionRequestRatingSearch();
    }

    /**
     * @return array
     */
    public function getFormConfig()
    {
        return [
            Yii::t('back/app', 'Main') => [
                'request_id' => [
                    'type' => FormBuilder::INPUT_DROPDOWN_LIST,
                    'items' => CompetitionRequest::getListItems('id', 'name'),
                    'options' => [
                        'prompt' => '',
                    ],
                ],
                'user_id' => [
                    'type' => FormBuilder::INPUT_DROPDOWN_LIST,
                    'items' => User::getListItems('id', 'email'),
                    'options' => [
                        'prompt' => '',
                    ],
                ],
                'rating' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
            ],
        ];
    }
}
