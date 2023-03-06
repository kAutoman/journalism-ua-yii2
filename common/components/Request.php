<?php

namespace common\components;

use yii\web\Request as BaseRequest;

/**
 * Class Request
 *
 * @package common\components
 */
class Request extends BaseRequest
{
    /**
     * @var array
     */
    public $acceptableLanguages;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        if (is_callable($this->acceptableLanguages)) {
            $this->setAcceptableLanguages(call_user_func($this->acceptableLanguages));
        }
    }
}
