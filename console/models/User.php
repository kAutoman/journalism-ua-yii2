<?php
namespace console\models;

use yii\behaviors\TimestampBehavior;

/**
 * User model for console app
 *
 * @author Vladimir Kuprienko <vldmr.kuprienko@gmail.com>
 */
class User extends \common\models\User
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => TimestampBehavior::class,
        ];
    }
}
