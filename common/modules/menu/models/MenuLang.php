<?php

namespace common\modules\menu\models;

use common\components\model\ActiveRecord;

/**
 * This is the model class for table "{{%menu_lang}}".
 *
 * @property integer $model_id
 * @property string $language
 * @property string $label
 */
class MenuLang extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%menu_lang}}';
    }
}
