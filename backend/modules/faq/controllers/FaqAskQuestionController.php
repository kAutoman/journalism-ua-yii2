<?php

namespace backend\modules\faq\controllers;

use backend\components\BackendController;
use backend\modules\faq\models\FaqAskQuestion;
/**
 * Class FaqAskQuestionController
 *
 * @package backend\modules\faq\models
 */
class FaqAskQuestionController extends BackendController
{
    public $canCreate = false;
    public $canDelete = false;
    public $canUpdate = false;

    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return FaqAskQuestion::class;
    }
}
