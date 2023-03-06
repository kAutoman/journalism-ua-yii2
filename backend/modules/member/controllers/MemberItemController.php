<?php

namespace backend\modules\member\controllers;

use backend\components\BackendController;
use backend\modules\member\models\MemberItem;

/**
 * Class MemberItemController
 *
 * @package backend\modules\member\models
 */
class MemberItemController extends BackendController
{
    public $canCreate = true;

    public $canDelete = false;

    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return MemberItem::class;
    }
}
