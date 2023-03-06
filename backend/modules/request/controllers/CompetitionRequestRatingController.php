<?php

namespace backend\modules\request\controllers;

use backend\components\BackendController;
use backend\modules\request\models\CompetitionRequestRating;
/**
 * Class CompetitionRequestRatingController
 *
 * @package backend\modules\request\models
 */
class CompetitionRequestRatingController extends BackendController
{
    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return CompetitionRequestRating::class;
    }
}
