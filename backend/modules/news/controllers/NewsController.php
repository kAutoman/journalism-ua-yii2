<?php

namespace backend\modules\news\controllers;

use backend\components\BackendController;
use backend\modules\news\models\News;
/**
 * Class NewsController
 *
 * @package backend\modules\news\models
 */
class NewsController extends BackendController
{
    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return News::class;
    }
}
