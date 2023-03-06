<?php

namespace backend\modules\request\controllers;

use backend\components\BackendController;
use backend\modules\request\models\RejectedCompetitionRequest;
use common\models\User;
use yii\filters\AccessControl;

/**
 * Class RejectedCompetitionRequestController
 * @package backend\modules\request\controllers
 */
class RejectedCompetitionRequestController extends BackendController
{
    public $canCreate = false;

    public $canDelete = false;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [User::ROLE_ADMIN, User::ROLE_JURY_ADMIN, User::ROLE_MODERATOR],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return RejectedCompetitionRequest::class;
    }
}
