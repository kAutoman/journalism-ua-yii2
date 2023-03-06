<?php

namespace backend\modules\user\controllers;

use backend\actions\ActionUpdate;
use backend\components\BackendController;
use backend\modules\user\models\User;

/**
 * UserController implements the CRUD actions for User model.
 *
 * @property string $modelClass
 * @property null|string $langModelClass
 */
class UserController extends BackendController
{
    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return User::class;
    }

    /**
     * @return null|string
     */
    public function getLangModelClass(): ?string
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return merge(parent::actions(), [
            'update' => [
                'scenario' => 'update',
                'formConfig' => 'getFormUpdate'
            ],
            'create' => [
                'scenario' => 'create',
                'formConfig' => 'getFormConfig'
            ],
            'change-password' => [
                'class' => ActionUpdate::class,
                'modelClass' => $this->getModelClass(),
                'scenario' => 'change-password',
                'formConfig' => 'getChangePasswordForm'
            ]
        ]);
    }
}
