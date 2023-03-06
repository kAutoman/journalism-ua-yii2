<?php

namespace backend\modules\language\controllers;

use backend\components\BackendController;
use backend\modules\language\models\SourceMessage;
use yii\helpers\ArrayHelper;

/**
 * TranslationController implements the CRUD actions for SourceMessage model.
 */
class TranslationController extends BackendController
{
    public $canCreate = false;

    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return SourceMessage::class;
    }
}
