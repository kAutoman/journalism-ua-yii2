<?php


namespace api\controllers;


use api\components\ActiveRestController;
use api\components\RestController;
use api\models\Login;
use api\models\Refresh;
use api\models\User;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\NotAcceptableHttpException;

class AuthController extends RestController
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

    /**
     * @return Login|User|bool
     * @throws InvalidConfigException
     */
    public function actionLogin()
    {
        $model = new Login();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        $model->validate();
        if ($user = $model->login()) {
            return $user;
        } else {
            return $model;
        }
    }

    /**
     * @return Refresh|User|bool
     * @throws NotAcceptableHttpException
     * @throws InvalidConfigException
     */
    public function actionRefresh()
    {
        $model = new Refresh();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if (!$model->validate()) {
            return $model;
        }
        $reset = $model->reset();
        if ($reset) {
            return $reset;
        }

        throw new NotAcceptableHttpException('Some gone bad');
    }
}
