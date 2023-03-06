<?php

namespace backend\modules\home\controllers;

use backend\components\BackendController;
use backend\modules\home\models\HomeHeaderSlider;
/**
 * Class HomeHeaderSliderController
 *
 * @package backend\modules\home\models
 */
class HomeHeaderSliderController extends BackendController
{
    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return HomeHeaderSlider::class;
    }
}
