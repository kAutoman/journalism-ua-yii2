<?php

namespace common\models;

use common\components\model\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%social}}".
 *
 * @property integer $id
 * @property string $link
 * @property integer $published
 * @property integer $position
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property EntityToFile $iconSrc
 */
class Social extends ActiveRecord
{
    const SAVE_ATTRIBUTE_ICON = 'SocialIcon';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%social}}';
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

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getIconSrc()
    {
        return $this->hasOne(EntityToFile::class, ['entity_model_id' => 'id'])
            ->andOnCondition([
                'icon.entity_model_name' => static::formName(),
                'icon.attribute' => static::SAVE_ATTRIBUTE_ICON
            ])
            ->alias('icon')
            ->orderBy('icon.position DESC');
    }
}
