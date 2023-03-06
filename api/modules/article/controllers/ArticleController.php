<?php

namespace api\modules\article\controllers;

use api\actions\ListAction;
use api\components\RestController;
use api\modules\article\entities\ArticleEntity;
use api\modules\article\models\Article;
use common\components\model\DefaultQuery;
use yii\rest\Serializer;
use yii\web\NotFoundHttpException;
use yii\web\Request;

/**
 * Class ArticleController
 * @package api\modules\article\controllers
 */
class ArticleController extends RestController
{
    public $serializer = [
        'class' => Serializer::class,
        'collectionEnvelope' => 'items'
    ];

    /**
     * @return array
     */
    public function actions()
    {
        return [
            'list' => [
                'class' => ListAction::class,
                'modelClass' => Article::class,
                'pageSize' => 6,
                'query' => function (DefaultQuery $query, Request $request) {
                    return $query
                        ->isPublished()
                        ->andWhere(['<=', 'publication_date', time()]);
                },
                'sort' => [
                    'defaultOrder' => [
                        'publication_date' => SORT_DESC,
                        'position' => SORT_ASC,
                        'id' => SORT_DESC,
                    ]
                ]
            ],
        ];
    }

    /**
     * @param string $alias
     *
     * @return Article|null
     * @throws NotFoundHttpException
     */
    public function actionView(string $alias): ?ArticleEntity
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

        return new ArticleEntity($model);
    }
}
