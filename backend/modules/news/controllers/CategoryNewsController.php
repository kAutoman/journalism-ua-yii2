<?php

namespace backend\modules\news\controllers;

use backend\components\BackendController;
use backend\modules\news\models\CategoryNews;
/**
 * Class CategoryNewsController
 *
 * @package backend\modules\news\models
 */
class CategoryNewsController extends BackendController
{
    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return CategoryNews::class;
    }
}
