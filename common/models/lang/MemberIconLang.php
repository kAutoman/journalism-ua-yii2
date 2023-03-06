<?php

namespace common\models\lang;

use common\components\model\ActiveRecord;

/**
 * This is the model class for table "{{%member_icon_lang}}".
 *
 * @property integer $model_id
 * @property string $language
 * @property string $description
 */
class MemberIconLang extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%member_icon_lang}}';
    }
}
