<?php

namespace backend\modules\language\controllers;

use yii\helpers\ArrayHelper;
use backend\actions\ActionCreate;
use backend\actions\ActionUpdate;
use backend\components\BackendController;
use backend\modules\language\models\Language;

/**
 * LanguageController implements the CRUD actions for Language model.
 */
class LanguageController extends BackendController
{
    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return Language::class;
    }

    /**
     * @return array
     * @inheritdoc
     */
    public function actions()
    {
        return ArrayHelper::merge(parent::actions(), [
            'update' => [
                'class' => ActionUpdate::class,
                'modelClass' => $this->getModelClass(),
                'enableAjaxValidation' => true
            ],
            'create' => [
                'class' => ActionCreate::class,
                'modelClass' => $this->getModelClass(),
                'enableAjaxValidation' => true
            ]
        ]);
    }
}
