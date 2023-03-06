<?php

namespace backend\modules\jury\controllers;

use backend\components\BackendController;
use backend\modules\jury\models\Jury;
/**
 * Class JuryController
 *
 * @package backend\modules\jury\models
 */
class JuryController extends BackendController
{
    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return Jury::class;
    }
}
