<?php


namespace api\components;


use yii\base\Action;
use yii\filters\auth\HttpBearerAuth;
use yii\web\UnauthorizedHttpException;

class ApiHttpBearerAuth extends HttpBearerAuth
{
    /**
     * @param Action $action
     * @return bool
     * @throws UnauthorizedHttpException
     */
    public function beforeAction($action)
    {
        if (request()->isOptions) {
            return true;
        } else {
            return parent::beforeAction($action);
        }
    }
}
