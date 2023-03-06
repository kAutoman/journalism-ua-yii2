<?php

namespace common\models;

use common\components\model\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\behaviors\TranslatedBehavior;
use common\interfaces\Translatable;
use common\models\lang\HomeHeaderSliderLang;
use lav45\translate\TranslatedTrait;

/**
 * This is the model class for table "{{%home_header_slider}}".
 *
 * @property integer $id
 * @property string $label
 * @property string $content
 * @property string $button_label
 * @property string $button_src
 * @property integer $button_form_enable
 * @property integer $published
 * @property integer $position
 * @property integer $created_at
 * @property integer $updated_at
 * @property EntityToFile $imageSrc
 */
class HomeHeaderSlider extends ActiveRecord implements Translatable
{
    const SAVE_ATTRIBUTE_IMAGE = 'HomeHeaderSliderImage';

    use TranslatedTrait;

    public $langModelClass = HomeHeaderSliderLang::class;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%home_header_slider}}';
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
            'translated' => [
                'class' => TranslatedBehavior::class,
                'translateAttributes' => $this->getLangAttributes()
            ],
        ];
    }

    /**
     * List of all translatable attributes from
     *
     * @return array
     */
    public function getLangAttributes(): array
    {
        return [
            'label',
            'content',
            'button_label',
            'button_src',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getImageSrc()
    {
        return $this->hasOne(EntityToFile::class, ['entity_model_id' => 'id'])
            ->andOnCondition([
                'image.entity_model_name' => static::formName(),
                'image.attribute' => static::SAVE_ATTRIBUTE_IMAGE
            ])
            ->alias('image')
            ->orderBy('image.position DESC');
    }
}
