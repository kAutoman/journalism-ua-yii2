<?php

namespace api\controllers;
use api\components\RestController;
use yii\db\Query;

class NominationsController extends RestController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        return $behaviors;
    }

    public function actions()
    {
        if (request()->isOptions) {
            response()->setStatusCode(200);
            response()->send();
            return [];
        }
        return parent::actions();
    }

    public function actionIndex()
    {
        $query = new Query;
        // compose the query
        $query->select('model_id, label')
            ->from('member_item_lang')
            ->join('LEFT JOIN', 'member_item', 'member_item.id = member_item_lang.model_id')
            ->where(['member_item.published'=>1]);
        // build and execute the query
        $rows = $query->all();
        return $rows;
    }

}