<?php

namespace backend\modules\home\controllers;

use backend\components\BackendController;
use backend\modules\home\models\HomeCouncilItem;
/**
 * Class HomeCouncilItemController
 *
 * @package backend\modules\home\models
 */
class HomeCouncilItemController extends BackendController
{
    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return HomeCouncilItem::class;
    }
}
