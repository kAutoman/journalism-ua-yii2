<?php

namespace backend\modules\home\controllers;

use backend\components\BackendController;
use backend\modules\home\models\HomePartnerItem;
/**
 * Class HomePartnerItemController
 *
 * @package backend\modules\home\models
 */
class HomePartnerItemController extends BackendController
{
    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return HomePartnerItem::class;
    }
}
