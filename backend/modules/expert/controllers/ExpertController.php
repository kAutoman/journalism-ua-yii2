<?php

namespace backend\modules\expert\controllers;

use backend\components\BackendController;
use backend\modules\expert\models\Expert;

/**
 * Class ExpertController
 *
 * @package backend\modules\expert\models
 */
class ExpertController extends BackendController
{
    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return Expert::class;
    }
}
