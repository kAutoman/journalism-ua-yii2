<?php

namespace api\modules\request;

use yii\base\Module;

class RequestModule extends Module
{

    /**
     * @var string
     */
    public $controllerNamespace = 'api\modules\request\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        return parent::init();
    }
}
