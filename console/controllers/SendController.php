<?php

namespace console\controllers;

use yii\console\Controller;
use common\modules\mailer\components\Mailer;

/**
 * Class SendController
 */
class SendController extends Controller
{
    /**
     * @return int
     */
    public function actionEmail()
    {
        $mailer = new Mailer();

        return $mailer->batchSend(10);
    }
}
