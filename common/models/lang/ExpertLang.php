<?php

namespace common\models\lang;

use common\components\model\ActiveRecord;

/**
 * This is the model class for table "{{%expert_lang}}".
 *
 * @property integer $model_id
 * @property string $language
 * @property string $name
 * @property string $staff
 */
class ExpertLang extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%expert_lang}}';
    }
}
