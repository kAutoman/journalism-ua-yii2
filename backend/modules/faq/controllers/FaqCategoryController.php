<?php

namespace backend\modules\faq\controllers;

use backend\components\BackendController;
use backend\modules\faq\models\FaqCategory;
use yii\helpers\ArrayHelper;
use backend\actions\ActionCreate;
use backend\actions\ActionUpdate;

/**
 * Class FaqCategoryController
 *
 * @package backend\modules\faq\models
 */
class FaqCategoryController extends BackendController
{
    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return FaqCategory::class;
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
