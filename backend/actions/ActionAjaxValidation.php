<?php

namespace backend\actions;

use Yii;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\web\BadRequestHttpException;
use common\components\model\ActiveRecord;

/**
 * Class ActionAjaxValidation
 *
 * @package backend\actions
 */
class ActionAjaxValidation extends ActionCRUD
{
    /**
     * Runs the action.
     *
     * @param mixed $id Primary key
     * @return array
     * @throws BadRequestHttpException
     */
    public function run($id = null)
    {
        $request = Yii::$app->getRequest();
        /** @var ActiveRecord $modelClass */
        $modelClass = $this->modelClass;
        $model = $id === null ? new $modelClass() : $modelClass::findOne($id);
        if ($request->getIsAjax() && $model->load($request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return ActiveForm::validate($model);
        }

        throw new BadRequestHttpException();
    }
}
