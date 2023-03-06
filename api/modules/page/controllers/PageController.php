<?php

namespace api\modules\page\controllers;

use api\components\RestController;
use api\modules\page\entities\PageEntity;
use api\modules\page\models\Page;
use yii\web\NotFoundHttpException;

/**
 * Class PageController
 *
 * @package api\modules\page\controllers
 */
class PageController extends RestController
{
    /**
     * @param string $alias
     *
     * @return PageEntity
     * @throws NotFoundHttpException
     */
    public function actionIndex(string $alias = '/')
    {
        $model = Page::find()
            ->isPublished()
            ->andWhere([
                'alias' => $alias
            ])
            ->one();

        if ($model === null) {
            throw new NotFoundHttpException();
        }

        return new PageEntity($model);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionHome()
    {
        return $this->actionIndex();
    }
}
