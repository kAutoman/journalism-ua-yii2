<?php

namespace backend\modules\seo\controllers;

use backend\components\BackendController;
use backend\modules\seo\models\MetaTags;
/**
 * Class MetaTagsController
 *
 * @package backend\modules\seo\models
 */
class MetaTagsController extends BackendController
{
    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return MetaTags::class;
    }
}
