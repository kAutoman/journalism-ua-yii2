<?php

namespace backend\modules\article\controllers;

use backend\components\BackendController;
use backend\modules\article\models\Article;

/**
 * Class ArticleController
 *
 * @package backend\modules\article\models
 */
class ArticleController extends BackendController
{
    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return Article::class;
    }
}
