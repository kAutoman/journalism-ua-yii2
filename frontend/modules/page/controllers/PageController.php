<?php

namespace frontend\modules\page\controllers;

use frontend\modules\article\models\Article;
use yii\data\Pagination;
use yii\web\Controller;

/**
 * Class ArticleController
 * @package api\modules\article\controllers
 */
class PageController extends Controller
{
    /**
     * @param string $alias
     *
     * @return string
     */
    public function actionView(?string $alias = null)
    {
        $alias = $alias ?? 'home';

        if ($alias == 'home') {
            $articles = Article::find()
                ->isPublished()
                ->orderBy(['publication_date' => SORT_DESC])
                ->andWhere(['<=', 'publication_date', time()])
                ->limit(2)
                ->all();
        }

        return $this->render($alias, [
            'articles' => $articles ?? null,
        ]);
    }
}
