<?php

namespace backend\modules\request;

use yii\base\Module;

class RequestModule extends Module
{

    /**
     * @var string
     */
    public $controllerNamespace = 'backend\modules\request\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        return parent::init();
    }
}
