<?php

namespace backend\modules\faq\controllers;

use backend\components\BackendController;
use backend\modules\faq\models\Faq;
use yii\helpers\ArrayHelper;
use backend\actions\ActionCreate;
use backend\actions\ActionUpdate;

/**
 * Class FaqController
 *
 * @package backend\modules\faq\models
 */
class FaqController extends BackendController
{
    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return Faq::class;
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
                'enableAjaxValidation' => true,
            ],
            'create' => [
                'class' => ActionCreate::class,
                'modelClass' => $this->getModelClass(),
                'enableAjaxValidation' => true
            ]
        ]);
    }
}
