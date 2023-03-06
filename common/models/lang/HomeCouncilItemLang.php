<?php

namespace common\models\lang;

use common\components\model\ActiveRecord;

/**
 * This is the model class for table "{{%home_council_item_lang}}".
 *
 * @property integer $model_id
 * @property string $language
 * @property string $label
 * @property string $description
 */
class HomeCouncilItemLang extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%home_council_item_lang}}';
    }
}
