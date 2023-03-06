<?php

namespace common\modules\builder\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\components\model\ActiveRecord;

/**
 * This is the model class for table "{{%builder_attribute}}".
 *
 * @property integer $id
 * @property integer $builder_id
 * @property string $language
 * @property string $attribute
 * @property string $value
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Builder $builder
 */
class BuilderAttribute extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%builder_attribute}}';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'timestamp' => TimestampBehavior::class
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('back/builder', 'ID'),
            'builder_id' => Yii::t('back/builder', 'Builder'),
            'language' => Yii::t('back/builder', 'Language'),
            'attribute' => Yii::t('back/builder', 'Attribute'),
            'value' => Yii::t('back/builder', 'Value'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuilder()
    {
        return $this->hasOne(Builder::class, ['id' => 'builder_id']);
    }
}
