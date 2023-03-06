<?php

namespace common\modules\dynamicForm;

use yii\base\Module;

/**
 * Class DynamicFormModule
 *
 * @package common\modules\dynamicForm
 */
class DynamicFormModule extends Module
{
    /**
     * @var string
     */
    public $controllerNamespace = 'common\modules\dynamicForm\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        return parent::init();
    }
}
