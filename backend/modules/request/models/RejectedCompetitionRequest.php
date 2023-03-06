<?php

namespace backend\modules\request\models;

use backend\components\FormBuilder;
use backend\modules\rbac\models\User;
use Yii;
use backend\components\grid\StylingActionColumn;
use backend\modules\member\models\MemberItem;

/**
 * Class RejectedCompetitionRequest
 *
 * @package backend\modules\request\models
 */
class RejectedCompetitionRequest extends CompetitionRequest
{
    /**
     * Get title for the template page
     *
     * @return string
     */
    public function getTitle()
    {
        return Yii::t('back/request', 'Rejected Competition Request');
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
                        'filter' => MemberItem::getListItems(),
                    ],
                    [
                        'class' => StylingActionColumn::class,
                        'template' => '{update} {delete}',
                        'visibleButtons' => [
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

    /**
     * @return CompetitionRequestSearch|RejectedCompetitionRequestSearch
     */
    public function getSearchModel()
    {
        return new RejectedCompetitionRequestSearch();
    }

    /**
     * @return array
     */
    public function getFormConfig()
    {
        $config = [
            'name' => [
                'type' => FormBuilder::INPUT_TEXT,
            ],
            'email' => [
                'type' => FormBuilder::INPUT_TEXT,
            ],
            'gender' => [
                'type' => FormBuilder::INPUT_TEXT,
            ],
            'age' => [
                'type' => FormBuilder::INPUT_TEXT,
            ],
            'city' => [
                'type' => FormBuilder::INPUT_TEXT,
            ],
            'company_name' => [
                'type' => FormBuilder::INPUT_TEXT,
            ],
            'position' => [
                'type' => FormBuilder::INPUT_TEXT,
            ],
            'phone' => [
                'type' => FormBuilder::INPUT_TEXT,
            ],
            'experience' => [
                'type' => FormBuilder::INPUT_TEXT,
            ],
            'other_name' => [
                'type' => FormBuilder::INPUT_TEXT,
            ],
            'material_label' => [
                'type' => FormBuilder::INPUT_TEXTAREA,
            ],
            'material_type' => [
                'type' => FormBuilder::INPUT_TEXT,
            ],
            'program_label' => [
                'type' => FormBuilder::INPUT_TEXT,
            ],
            'program_published_date' => [
                'type' => FormBuilder::INPUT_TEXT,
            ],
            'program_link' => [
                'type' => FormBuilder::INPUT_TEXTAREA,
            ],
            'nomination' => [
                'type' => FormBuilder::INPUT_TEXTAREA,
            ],
            'nomination_id' => [
                'type' => FormBuilder::INPUT_DROPDOWN_LIST,
                'items' => \common\models\MemberItem::getListItems(),
                'options' => ['prompt' => '']
            ],
            'argument' => [
                'type' => FormBuilder::INPUT_TEXTAREA,
            ],
            'awards' => [
                'type' => FormBuilder::INPUT_TEXTAREA,
            ],
            'status' => [
                'type' => FormBuilder::INPUT_DROPDOWN_LIST,
                'items' => self::getStatuses(),
                'options' => ['disabled' => !user()->can(User::ROLE_JURY_ADMIN),],
            ],
        ];

        return [Yii::t('back/app', 'Main') => $config];
    }
}
