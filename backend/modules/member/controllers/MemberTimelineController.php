<?php

namespace backend\modules\member\controllers;

use backend\components\BackendController;
use backend\modules\member\models\MemberTimeline;
/**
 * Class MemberTimelineController
 *
 * @package backend\modules\member\models
 */
class MemberTimelineController extends BackendController
{
    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return MemberTimeline::class;
    }
}
