<?php

namespace backend\modules\member\controllers;

use backend\components\BackendController;
use backend\modules\member\models\MemberIcon;
/**
 * Class MemberIconController
 *
 * @package backend\modules\member\models
 */
class MemberIconController extends BackendController
{
    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return MemberIcon::class;
    }
}
