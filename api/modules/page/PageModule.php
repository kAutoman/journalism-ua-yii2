<?php

namespace api\modules\page;

use yii\base\Module;

/**
 * Class PageModule
 *
 * @package api\modules\page
 */
class PageModule extends Module
{
    /**
     * @var string
     */
    public $controllerNamespace = 'api\modules\page\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        return parent::init();
    }
}
