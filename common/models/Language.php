<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\components\model\ActiveRecord;

/**
 * Class Language
 * @package common\models
 *
 * @property int $id
 * @property string $label
 * @property string $code
 * @property string $region
 * @property string $locale
 * @property boolean $published
 * @property string $position
 * @property string $is_default
 */
class Language extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%language}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
            ],
        ];
    }

    public function getLocale()
    {
        return $this->code . '_' . $this->region;
    }

    public function afterDelete()
    {
        Yii::$app->cacheLang->delete('languages');
        parent::afterDelete();
    }

    public function afterSave($insert, $changedAttributes)
    {
        Yii::$app->cacheLang->delete('languages');
        parent::afterSave($insert, $changedAttributes);
    }
}
