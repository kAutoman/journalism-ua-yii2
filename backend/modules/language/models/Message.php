<?php

namespace backend\modules\language\models;

use common\components\model\ActiveRecord;


/**
 * Class Message
 *
 * @property int $id
 * @property string $language
 * @property string $translation
 *
 * @package backend\modules\language\models
 */
class Message extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%message}}';
    }
}
