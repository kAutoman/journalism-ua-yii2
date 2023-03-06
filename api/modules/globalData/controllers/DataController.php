<?php

namespace api\modules\globalData\controllers;

use api\components\RestController;
use api\components\RestSerializer;
use api\modules\globalData\entities\GlobalDataEntity;

/**
 * Class DataController
 *
 * @package api\modules\globalData\controllers
 */
class DataController extends RestController
{
    public $serializer = RestSerializer::class;

    public function actionGlobal()
    {
        return new GlobalDataEntity();
    }
}
