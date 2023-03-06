<?php

namespace common\models\lang;

use common\components\model\ActiveRecord;

/**
 * This is the model class for table "{{%member_item_lang}}".
 *
 * @property integer $model_id
 * @property string $language
 * @property string $label
 * @property string $content
 */
class MemberItemLang extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%member_item_lang}}';
    }
}
