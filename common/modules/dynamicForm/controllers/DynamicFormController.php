<?php

namespace common\modules\dynamicForm\controllers;

use common\modules\dynamicForm\components\Model;
use common\modules\dynamicForm\widgets\DynamicForm;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\helpers\Json;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Class DynamicFormController
 *
 * @package common\modules\dynamicForm\controllers
 */
class DynamicFormController extends Controller
{
    /**
     * @return string
     */
    public function actionNewRow()
    {
        $request = Yii::$app->getRequest();
        $className = $request->post('className');
        $container = $request->post('container');
        $index = $request->post('index');
        $relModels[$index] = new $className;
        /** @var \common\components\model\ActiveRecord $newModel */
        $newModel = $relModels[$index];
        $newModel->loadDefaultValues();
        $newModel->relModelIndex = $index;
        return Json::encode([
            'replaces' => [
                [
                    'what' => ".$container .df-widget-item:last-child",
                    'data' => $this->renderAjax(DynamicForm::FIELD_VIEW_FILE, ['relatedModels' => $relModels])
                ]
            ]
        ]);
    }
}
