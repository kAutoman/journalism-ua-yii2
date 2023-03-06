<?php
namespace console\controllers;

use common\models\IpBlock;
use yii\console\Controller;
use yii\db\Query;
use yii\helpers\Console;

/**
 * Class IpController
 *
 * @package console\controllers
 */
class IpController extends Controller
{
    /**
     * @var string
     */
    public $defaultAction = 'check';

    /**
     * @param int $timeout
     */
    public function actionCheck($timeout = 86400)
    {
        $time = time() - $timeout;

        /** @var Query $query */
        $query = IpBlock::find();

        $query->andWhere(['<=', 'date', $time]);

        /** @var IpBlock[] $models */
        $models = $query->all();

        foreach ($models as $model) {
            if ($model->delete()) {
                $this->stdout("Ip {$model->ip} successfully unlocked.\n", Console::FG_GREEN);
            } else {
                $this->stdout("Error unblocking ip {$model->ip}.\n", Console::FG_RED);
            }
        }
    }

    /**
     * @param $email
     */
    public function actionUnBlock($ip)
    {
        $ipBlock = IpBlock::findOne(['ip' => $ip]);

        if ($ipBlock) {
            if ($ipBlock->delete()) {
                $this->stdout("Ip successfully unlocked.\n", Console::FG_GREEN);
            } else {
                $this->stdout("Error unblocking ip.\n", Console::FG_RED);
            }
        } else {
            $this->stdout("Ip not found.\n", Console::FG_RED);
        }
    }
}
