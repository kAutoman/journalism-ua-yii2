<?php

namespace common\modules\builder\models;

use common\helpers\UrlHelper;
use Yii;
use yii\behaviors\TimestampBehavior;
use common\components\model\ActiveRecord;

/**
 * This is the model class for table "{{%builder}}".
 *
 * @property integer $id
 * @property string $language
 * @property string $builder_model_class
 * @property string $target_class
 * @property integer $target_id
 * @property string $target_sign
 * @property string $target_attribute
 * @property string $tag_level
 * @property string $component_name
 * @property integer $position
 * @property integer $created_at
 * @property integer $updated_at
 * @property boolean $published
 *
 * @property BuilderAttribute[] $builderAttributes
 */
class Builder extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%builder}}';
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
            'language' => Yii::t('back/builder', 'Language'),
            'builder_model_class' => Yii::t('back/builder', 'Common builder model class name'),
            'target_class' => Yii::t('back/builder', 'Target class'),
            'target_id' => Yii::t('back/builder', 'Target entity ID'),
            'target_sign' => Yii::t('back/builder', 'Target sign'),
            'target_attribute' => Yii::t('back/builder', 'Target attribute'),
            'tag_level' => Yii::t('back/builder', 'Tag level'),
            'position' => Yii::t('back/builder', 'Position'),
            'component_name' => Yii::t('back/builder', 'Custom component name'),
            'published' => Yii::t('back/builder', 'Published'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuilderAttributes()
    {
        return $this->hasMany(BuilderAttribute::class, ['builder_id' => 'id']);
    }
}
