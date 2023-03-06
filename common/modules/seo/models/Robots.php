<?php

namespace common\modules\seo\models;

use common\components\model\ActiveRecord;

/**
 * This is the model class for table "{{%robots}}".
 *
 * @property integer $id
 * @property string $text
 */
class Robots extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%robots}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [];
    }
}
