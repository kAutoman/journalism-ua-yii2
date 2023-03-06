<?php

namespace api\modules\globalData;

use yii\base\Module;

/**
 * Class GlobalDataModule
 *
 * @package api\modules\globalData
 */
class GlobalDataModule extends Module
{
    /**
     * @var string
     */
    public $controllerNamespace = 'api\modules\globalData\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        return parent::init();
    }
}
