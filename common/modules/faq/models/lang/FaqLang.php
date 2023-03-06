<?php

namespace common\modules\faq\models\lang;

use common\components\model\ActiveRecord;

/**
 * This is the model class for table "{{%faq_lang}}".
 *
 * @property integer $model_id
 * @property string $language
 * @property string $question
 * @property string $answer
 */
class FaqLang extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%faq_lang}}';
    }
}
