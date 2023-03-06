<?php

namespace common\models\lang;

use common\components\model\ActiveRecord;

/**
 * This is the model class for table "{{%jury_lang}}".
 *
 * @property integer $model_id
 * @property string $language
 * @property string $name
 * @property string $description
 */
class JuryLang extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%jury_lang}}';
    }
}
