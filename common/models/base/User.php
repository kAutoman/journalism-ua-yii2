<?php

namespace common\models\base;

use common\components\IpLogBehavior;
use common\components\model\ActiveRecord;
use common\models\UserAuthLog;
use Yii;
use yii\base\NotSupportedException;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use yii2tech\authlog\AuthLogIdentityBehavior;

/**
 * This is the model class for table "{{%user}}".
 *

 * @property boolean $api_access
 */
abstract class User extends ActiveRecord implements IdentityInterface
{

}
