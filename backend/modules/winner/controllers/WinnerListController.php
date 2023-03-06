<?php

namespace backend\modules\winner\controllers;

use backend\components\BackendController;
use backend\modules\winner\models\WinnerList;
/**
 * Class WinnerListController
 *
 * @package backend\modules\winner\models
 */
class WinnerListController extends BackendController
{
    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return WinnerList::class;
    }
}
