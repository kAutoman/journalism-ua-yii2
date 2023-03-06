<?php

namespace common\models\lang;

use common\components\model\ActiveRecord;

/**
 * This is the model class for table "{{%page_lang}}".
 *
 * @property integer $model_id
 * @property string $language
 * @property string $label
 * @property string $content
 */
class PageLang extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%page_lang}}';
    }
}
