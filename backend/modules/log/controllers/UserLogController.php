<?php

namespace backend\modules\log\controllers;

use backend\components\BackendController;
use backend\modules\log\models\UserLog;
/**
 * Class UserLogController
 *
 * @package backend\modules\log\models
 */
class UserLogController extends BackendController
{
    public $canDelete = false;
    public $canCreate = false;
    public $canUpdate = false;
    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return UserLog::class;
    }
}
