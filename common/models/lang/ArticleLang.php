<?php

namespace common\models\lang;

use common\components\model\ActiveRecord;

/**
 * This is the model class for table "{{%article_lang}}".
 *
 * @property integer $model_id
 * @property string $language
 * @property string $label
 */
class ArticleLang extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%article_lang}}';
    }
}
