<?php

namespace backend\modules\page\controllers;

use backend\actions\ActionDelete;
use backend\actions\ActionUpdate;
use backend\components\BackendController;
use backend\modules\page\models\Page;
use backend\modules\page\widgets\TreeView;
use common\helpers\LanguageHelper;
use paulzi\nestedsets\NestedSetsBehavior;
use Yii;
use yii\base\InvalidArgumentException;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\log\Logger;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class PageController
 *
 * @package backend\modules\page\models
 */
class PageController extends BackendController
{
    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return Page::class;
    }

    public function actions()
    {
        return ArrayHelper::merge(parent::actions(), [
            'index' => null,
            'create' => null,
            'update' => [
                'class' => ActionUpdate::class,
                'view' => 'tree',
                'redirect' => [
                    'update',
                    'id' => request()->get('id'),
                    urlManager()->langParam => LanguageHelper::getEditLanguage()
                ]
            ],
            'delete' => null
        ]);
    }

    /**
     * @return Response
     */
    public function actionIndex()
    {
        return $this->redirect(['update', 'id' => Page::HOME_PAGE_ID]);
    }

    /**
     * @param int $id Current parent model ID
     * @param string $location
     *
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionCreate(int $id, string $location)
    {
        /** @var Page | NestedSetsBehavior $model */
        $model = new Page();
        /** @var Page | NestedSetsBehavior $relatedModel */
        $relatedModel = Page::findOneOrFail(['id' => $id]);

        $created = false;
        $errorMsg = false;

        if ($model->load(request()->post())) {

            switch ($location) {
                case TreeView::INSERT_ROOT:
                    $created = $model->makeRoot()->save();
                    break;
                case TreeView::INSERT_CHILD:
                    $created = $model->appendTo($relatedModel)->save();
                    break;
                case TreeView::INSERT_AFTER_ITEM:
                    $created = $model->insertAfter($relatedModel)->save();
                    break;
                case TreeView::INSERT_BEFORE_ITEM:
                    $created = $model->insertBefore($relatedModel)->save();
                    break;
                case TreeView::INSERT_BEGIN_LIST:
                    $parentNode = $relatedModel->getParent()->one();
                    if ($parentNode !== null) {
                        $created = $model->prependTo($parentNode)->save();
                    } else {
                        $errorMsg = 'Item is a root already';
                    }
                    break;
                case TreeView::INSERT_END_LIST:
                    $parentNode = $relatedModel->getParent()->one();
                    if ($parentNode !== null) {
                        $created = $model->appendTo($parentNode)->save();
                    } else {
                        $errorMsg = 'Item is a root already';
                    }
                    break;
                default:
                    Yii::getLogger()->log($model->getFirstErrors(), Logger::LEVEL_ERROR);
                    throw new InvalidArgumentException('Unknown movement direction');
            }
            if ($created && $errorMsg === false) {
                session()->addFlash('success', 'Success!');
                return $this->redirect(['update', 'id' => $model->id]);
            } else {
                session()->addFlash('warning', $errorMsg);
                return $this->redirect(['update', 'id' => $id]);
            }
        }

        return $this->render('tree', ['model' => $model]);
    }

    /**
     * @param int $id Current model ID
     * @param string $direction
     *
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionMove(int $id, string $direction)
    {
        /** @var NestedSetsBehavior | Page $currentModel */
        $currentModel = Page::findOneOrFail(['id' => $id]);
        $move = false;
        $errorMsg = false;

        switch ($direction) {
            case TreeView::MOVE_DIRECTION_UP:
                /** @var NestedSetsBehavior|Page $prevNode */
                $prevNode = $currentModel->getPrev()->one();
                if ($prevNode !== null) {
                    $move = $currentModel->insertBefore($prevNode)->save();
                } else {
                    $errorMsg = 'Item is a first child already';
                }
                break;
            case TreeView::MOVE_DIRECTION_DOWN:
                /** @var NestedSetsBehavior|Page $nextNode */
                $nextNode = $currentModel->getNext()->one();
                if ($nextNode !== null) {
                    $move = $currentModel->insertAfter($nextNode)->save();
                } else {
                    $errorMsg = 'Item is a last child already';
                }
                break;
            case TreeView::MOVE_DIRECTION_LEFT:
                /** @var NestedSetsBehavior|Page $parentNode */
                $parentNode = $currentModel->getParent()->one();
                if ($parentNode !== null && !$parentNode->isRoot()) {
                    $move = $currentModel->insertAfter($parentNode)->save();
                } else {
                    $errorMsg = 'Item is already root or can not be placed on root level';
                }
                break;
            case TreeView::MOVE_DIRECTION_RIGHT:
                /** @var NestedSetsBehavior|Page $prevNode */
                $prevNode = $currentModel->getPrev()->one();
                if ($prevNode !== null && $currentModel->depth < $this->module->maxDepth) {
                    $move = $currentModel->appendTo($prevNode)->save();
                } else {
                    $errorMsg = 'Item should be located right after parent or max depth is reached';
                }
                break;
            default:
                throw new InvalidArgumentException('Unknown movement direction');
        }

        if ($move && $errorMsg === false) {
            session()->addFlash('success', 'Success!');
        } else {
            session()->addFlash('warning', $errorMsg);
        }

        return $this->redirect(['update', 'id' => $id]);
    }

    /**
     * @param int $id
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionUpdatePage(int $id)
    {
        /** @var NestedSetsBehavior|Page $model */
        $model = Page::findOneOrFail(['id' => $id]);

        $model->updatePage = true;

        if ($model->load(request()->post())) {
            if ($model->validate() && $model->save()) {
                session()->addFlash('success', 'Success!');
                return $this->redirect(['update', 'id' => $model->id]);
            }
        }

        return $this->render('tree', ['model' => $model]);
    }

    /**
     * @param $id
     *
     * @return Response
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionDelete($id)
    {
        /** @var Page | NestedSetsBehavior $model */
        $model = Page::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException();
        }
        if ($model->deleteWithChildren()) {
            Yii::$app->getSession()->setFlash('danger', Yii::t('back/app', 'Record successfully deleted!'));
        }

        return $this->redirect(['update', 'id' => 1]);
    }
}
