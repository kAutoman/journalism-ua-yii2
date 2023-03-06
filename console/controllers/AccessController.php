<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\rbac\DbManager;

/**
 * Class AccessController
 *
 * @package console\controllers
 */
class AccessController extends Controller
{
    /**
     * @var DbManager
     */
    public $authManager;

    public function init()
    {
        $this->authManager = Yii::$app->getAuthManager();

        return parent::init();
    }

    public function actionInit()
    {
        $authManager = $this->authManager;

        $authManager->removeAll();

    }
}
