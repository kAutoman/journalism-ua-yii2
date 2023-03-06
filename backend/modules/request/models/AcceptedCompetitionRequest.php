<?php

namespace backend\modules\request\models;

use backend\components\FormBuilder;
use backend\modules\member\models\MemberItem;
use backend\modules\rbac\models\User;
use Yii;
use backend\components\grid\StylingActionColumn;
use yii\db\Query;

/**
 * Class AcceptedCompetitionRequest
 *
 * @package backend\modules\request\models
 */
class AcceptedCompetitionRequest extends CompetitionRequest
{
    public $rating;

    public $summ_rating;

    public function rules()
    {
        return merge(parent::rules(), [
            [
                'rating',
                'required',
                'when' => function (self $model) {
                    if (user()->can(User::ROLE_JURY_ADMIN)) {
                        return !user()->can(User::ROLE_JURY_ADMIN);
                    } else {
                        return user()->can(User::ROLE_JURY);
                    }
                }
            ],
        ]);
    }

    /**
     * Get title for the template page
     *
     * @return string
     */
    public function getTitle()
    {
        return Yii::t('back/request', 'Accepted Competition Request');
    }

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();

        $labels['rating'] = Yii::t('back/request', 'Rating');
        $labels['summ_rating'] = Yii::t('back/request', 'Summ rating');

        return $labels;
    }

    /**
     * Get attribute columns for index and view page
     *
     * @param $page
     *
     * @return array
     * @throws \Throwable
     */
    public function getColumns($page)
    {
        switch ($page) {
            case 'index':
                return [
                    ['class' => 'yii\grid\SerialColumn'],
                    'created_at:datetime',
                    'name',
                    'email',
                    'company_name',
                    'phone',
                    [
                        'attribute' => 'nomination_id',
                        'value' => function (self $model) {
                            return $model->nominationItem->label ?? null;
                        },
                        'filter' => MemberItem::getListByUser(),
                    ],
                    [
                        'attribute' => 'rating',
                        'visible' => user()->can(User::ROLE_JURY),
                    ],
                    [
                        'attribute' => 'summ_rating',
                        'visible' => user()->can(User::ROLE_JURY_ADMIN),
                    ],
                    [
                        'class' => StylingActionColumn::class,
                        'template' => '{rating} {update} {delete}',
                        'visibleButtons' => [
                            'rating' => user()->can(User::ROLE_JURY_ADMIN),
                            'delete' => user()->can(User::ROLE_JURY_ADMIN),
                        ]
                    ],
                ];
                break;
            case 'view':
                return [
                    'id',
                    'name',
                    'email',
                    'gender',
                    'age',
                    'city',
                    'position',
                    'company_name',
                    'phone',
                    'experience',
                    'other_name',
                    'material_label',
                    'material_type',
                    'program_label',
                    'program_published_date',
                    'program_link',
                    'nomination',
                    'argument',
                    'awards',
                ];
                break;
        }

        return [];
    }

    public function getExportConfig()
    {
        return [
            'name',
            'email',
            'company_name',
            'phone',
            'material_label',
            'nominationItem.label',
            'summ_rating',
        ];
    }

    /**
     * @param Query $query
     *
     * @return Query
     */
    public function getExportQuery(Query $query)
    {
        return $query
            ->andWhere(['status' => self::STATUS_ACCEPT])
            ->orderBy(['id' => SORT_DESC]);
    }

    /**
     * @return AcceptedCompetitionRequestSearch|CompetitionRequestSearch
     */
    public function getSearchModel()
    {
        return new AcceptedCompetitionRequestSearch();
    }

    public function getFormConfig()
    {
        $config = [
            'name' => [
                'type' => FormBuilder::INPUT_TEXT,
                'options' => ['readonly' => true,],
            ],
            'email' => [
                'type' => FormBuilder::INPUT_TEXT,
                'options' => ['readonly' => true,],
            ],
            'gender' => [
                'type' => FormBuilder::INPUT_TEXT,
                'options' => ['readonly' => true,],
            ],
            'age' => [
                'type' => FormBuilder::INPUT_TEXT,
                'options' => ['readonly' => true,],
            ],
            'city' => [
                'type' => FormBuilder::INPUT_TEXT,
                'options' => ['readonly' => true,],
            ],
            'company_name' => [
                'type' => FormBuilder::INPUT_TEXT,
                'options' => ['readonly' => true,],
            ],
            'position' => [
                'type' => FormBuilder::INPUT_TEXT,
                'options' => ['readonly' => true,],
            ],
            'phone' => [
                'type' => FormBuilder::INPUT_TEXT,
                'options' => ['readonly' => true,],
            ],
            'experience' => [
                'type' => FormBuilder::INPUT_TEXT,
                'options' => ['readonly' => true,],
            ],
            'other_name' => [
                'type' => FormBuilder::INPUT_TEXT,
                'options' => ['readonly' => true,],
            ],
            'material_label' => [
                'type' => FormBuilder::INPUT_TEXTAREA,
                'options' => ['readonly' => true,],
            ],
            'material_type' => [
                'type' => FormBuilder::INPUT_TEXT,
                'options' => ['readonly' => true,],
            ],
            'program_label' => [
                'type' => FormBuilder::INPUT_TEXT,
                'options' => ['readonly' => true,],
            ],
            'program_published_date' => [
                'type' => FormBuilder::INPUT_TEXT,
                'options' => ['readonly' => true,],
            ],
            'program_link' => [
                'type' => FormBuilder::INPUT_TEXTAREA,
                'options' => ['readonly' => true,],
            ],
            'nomination' => [
                'type' => FormBuilder::INPUT_TEXTAREA,
                'options' => ['readonly' => true,],
            ],
            'nomination_id' => [
                'type' => FormBuilder::INPUT_DROPDOWN_LIST,
                'items' => \common\models\MemberItem::getListItems(),
                'options' => ['prompt' => '', 'disabled' => true,]
            ],
            'argument' => [
                'type' => FormBuilder::INPUT_TEXTAREA,
                'options' => ['readonly' => true,],
            ],
            'awards' => [
                'type' => FormBuilder::INPUT_TEXTAREA,
                'options' => ['readonly' => true,],
            ],
            'status' => [
                'type' => FormBuilder::INPUT_DROPDOWN_LIST,
                'items' => self::getStatuses(),
                'options' => ['disabled' => !user()->can(User::ROLE_JURY_ADMIN),],
            ],
        ];

        if (user()->can(User::ROLE_JURY)) {
            $config['rating'] = [
                'type' => FormBuilder::INPUT_DROPDOWN_LIST,
                'items' => array_combine(range(1, 10), range(1, 10)),
                'options' => [
                    'prompt' => Yii::t('back/app', 'Select rating'),
                ],
            ];
        }

        return [Yii::t('back/app', 'Main') => $config];
    }

    public function afterFind()
    {
        parent::afterFind();

        /** @var CompetitionRequestRating $rating */
        $rating = CompetitionRequestRating::findOne([
            'request_id' => $this->id,
            'user_id' => user()->getId(),
        ]);

        if ($rating) {
            $this->rating = $rating->rating;
        }

        /** @var CompetitionRequestRating[] $ratings */
        $ratings = CompetitionRequestRating::findAll([
            'request_id' => $this->id,
        ]);

        if ($ratings) {
            $ratings = array_map(function (CompetitionRequestRating $model) { return $model->rating; }, $ratings);
            $this->summ_rating = array_sum($ratings);
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        /** @var CompetitionRequestRating $rating */
        $rating = CompetitionRequestRating::findOne([
            'request_id' => $this->id,
            'user_id' => user()->getId(),
        ]);

        if (!$rating) {
            $rating = new CompetitionRequestRating();

            $rating->request_id = $this->id;
            $rating->user_id = user()->getId();
        }

        $rating->rating = $this->rating;

        $rating->save();
    }
}
