<?php

namespace common\models\lang;

use common\components\model\ActiveRecord;

/**
 * This is the model class for table "{{%home_header_slider_lang}}".
 *
 * @property integer $model_id
 * @property string $language
 * @property string $label
 * @property string $content
 * @property string $button_label
 * @property string $button_src
 */
class HomeHeaderSliderLang extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%home_header_slider_lang}}';
    }
}
