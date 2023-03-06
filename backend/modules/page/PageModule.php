<?php

namespace backend\modules\page;

use yii\base\Module;

class PageModule extends Module
{
    /**
     * Determine multiple roots for one entity
     *
     * @var bool
     */
    public $singleRoot = true;

    /**
     * Max tree depth amount
     *
     * @var int
     */
    public $maxDepth = 3;

    /**
     * @var string
     */
    public $controllerNamespace = 'backend\modules\page\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        return parent::init();
    }
}
