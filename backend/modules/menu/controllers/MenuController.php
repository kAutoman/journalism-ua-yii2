<?php

namespace backend\modules\menu\controllers;

use backend\modules\menu\models\Menu;
use backend\components\BackendController;

/**
 * Class MenuController
 *
 * @package backend\modules\menu\models
 */
class MenuController extends BackendController
{
    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return Menu::class;
    }
}
