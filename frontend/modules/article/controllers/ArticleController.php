<?php

namespace frontend\modules\article\controllers;

use api\actions\ListAction;
use api\components\RestController;
use common\modules\builder\models\Builder;
use frontend\modules\article\entities\ArticleEntity;
use frontend\modules\article\models\Article;
use common\components\model\DefaultQuery;
use Yii;
use yii\data\Pagination;
use yii\helpers\VarDumper;
use yii\rest\Serializer;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Request;

/**
 * Class ArticleController
 * @package api\modules\article\controllers
 */
class ArticleController extends Controller
{
    public function actionList()
    {
        $query = Article::find()
            ->isPublished()
            ->orderBy(['publication_date' => SORT_DESC])
            ->andWhere(['<=', 'publication_date', time()]);

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $pages->defaultPageSize = 6;
        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        return $this->render('news', [
            'models' => $models,
            'pages' => $pages,
        ]);
    }

    /**
     * @param string $alias
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView(string $alias)
    {
        /** @var Article|null $model */
        $model = Article::find()
            ->andWhere([
                'alias' => $alias,
                'published' => true
            ])
            ->andWhere(['<=', 'publication_date', time()])
            ->one();

        if (!$model) {
            throw new NotFoundHttpException();
        }

        return $this->render('single', [
            'entity' => $model,
            'model' => new ArticleEntity($model),
        ]);
    }
}
