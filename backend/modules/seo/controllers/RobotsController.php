<?php

namespace backend\modules\seo\controllers;


use backend\components\BackendController;
use backend\modules\seo\models\Robots;
use yii\web\NotFoundHttpException;

/**
 * RobotsController implements the CRUD actions for Robots model.
 */
class RobotsController extends BackendController
{
    public $canCreate = false;
    public $canUpdate = false;
    public $canDelete = false;
    /**
     * @return string
     */
    public function getModelClass() : string
    {
        return Robots::class;
    }

    /**
     * Remove all pre-defined actions
     * @return array
     */
    public function actions()
    {
        return [];
    }

    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex()
    {
        $robots = Robots::findOneOrFail(['id' => 1]);

        if (request()->getIsPost() && $robots->load(request()->post())) {
            $robots->save();
            return $this->redirect('index');
        }
        $this->getView()->params['hideBreadcrumbs'] = false;

        return $this->render('//templates/update', [
            'formConfig' => 'getFormConfig',
            'enableAjaxValidation' => false,
            'model' => $robots,
        ]);
    }
}
