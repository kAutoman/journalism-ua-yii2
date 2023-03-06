<?php

namespace backend\modules\user\controllers;

use backend\modules\user\models\Permission;
use developeruz\db_rbac\controllers\AccessController as BaseAccessController;
use Yii;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * Class AccessController
 *
 * @package backend\modules\user\controllers
 */
class PermissionController extends BaseAccessController
{


    public $canCreate = true;

    public function actionIndex()
    {
        $searchModel = new Permission();
        $dataProvider = $searchModel->getDataProvider();

        return $this->render('//templates/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionCreate()
    {
        $model = new Permission();

        if (Yii::$app->getRequest()->getIsPost()) {
            var_dump($model->name);die;
            $permission = $this->clear($model->name);
            if ($permission && $this->validate($permission, $this->pattern4Permission) && $this->isUnique($permission)) {
                var_dump($permission);die;
                $model->createPermission($permission);

                return $this->redirect('index');
            }
        }
        return $this->render('//templates/create', [
            'enableAjaxValidation' => false,
            'model' => $model,
            'formConfig' => 'getFormConfig'
        ]);
    }
}
