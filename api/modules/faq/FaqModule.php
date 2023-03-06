<?php

namespace api\modules\faq;

use common\modules\faq\FaqModule as CommonFaqModuleAlias;

/**
 * Class FaqModule
 *
 * @package api\modules\faq
 */
class FaqModule extends CommonFaqModuleAlias
{
    /**
     * @var string
     */
    public $controllerNamespace = 'api\modules\faq\controllers';
}
