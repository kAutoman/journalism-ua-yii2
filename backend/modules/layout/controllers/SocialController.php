<?php

namespace backend\modules\layout\controllers;

use backend\components\BackendController;
use backend\modules\layout\models\Social;
/**
 * Class SocialController
 *
 * @package backend\modules\layout\models
 */
class SocialController extends BackendController
{
    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return Social::class;
    }
}
