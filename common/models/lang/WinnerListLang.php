<?php

namespace common\models\lang;

use common\components\model\ActiveRecord;

/**
 * This is the model class for table "{{%winner_list_lang}}".
 *
 * @property integer $model_id
 * @property string $language
 * @property string $name
 * @property string $publication_label
 */
class WinnerListLang extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%winner_list_lang}}';
    }
}
