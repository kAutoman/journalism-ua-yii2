<?php

namespace backend\modules\request\controllers;

use backend\components\BackendController;
use backend\modules\request\models\AcceptedCompetitionRequest;
use common\models\User;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

/**
 * Class AcceptedCompetitionRequestController
 *
 * @package backend\modules\request\controllers
 */
class AcceptedCompetitionRequestController extends BackendController
{
    public $canCreate = false;

    public $canDelete = false;

    public $canExport = false;

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
                        'roles' => [User::ROLE_ADMIN, User::ROLE_JURY_ADMIN, User::ROLE_JURY, User::ROLE_MODERATOR],
                    ],
                ],
            ],
        ];
    }

    public function init()
    {
        parent::init();

        if (user()->can(User::ROLE_JURY_ADMIN)) {
            $this->canExport = true;
        }
    }

    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return AcceptedCompetitionRequest::class;
    }

    /**
     * @param int|null $id
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionRating(?int $id)
    {
        $model = AcceptedCompetitionRequest::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        return $this->render('rating', ['model' => $model]);
    }
}
