<?php

namespace api\modules\seo;

use common\modules\seo\SeoModule as CommonSeoModule;

/**
 * Class SeoModule
 *
 * @package api\modules\seo
 */
class SeoModule extends CommonSeoModule
{
    /**
     * @var string
     */
    public $controllerNamespace = 'api\modules\seo\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        return parent::init();
    }
}
