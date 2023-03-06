<?php

namespace common\models\lang;

use common\components\model\ActiveRecord;

/**
 * This is the model class for table "{{%category_news_lang}}".
 *
 * @property integer $model_id
 * @property string $language
 * @property string $label
 * @property string $content
 */
class CategoryNewsLang extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%category_news_lang}}';
    }
}
