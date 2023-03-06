<?php

namespace common\modules\builder;

use yii\base\Module;

/**
 * Class BuilderModule
 *
 * @package common\modules\builder
 */
class BuilderModule extends Module
{
    /**
     * @var string
     */
    public $controllerNamespace = 'common\modules\builder\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        return parent::init();
    }
}
