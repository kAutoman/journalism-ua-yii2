<?php

namespace common\models;

use common\components\model\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\behaviors\TranslatedBehavior;
use common\interfaces\Translatable;
use common\models\lang\MemberItemLang;
use lav45\translate\TranslatedTrait;

/**
 * This is the model class for table "{{%member_item}}".
 *
 * @property integer $id
 * @property string $label
 * @property string $content
 * @property integer $published
 * @property integer $position
 * @property integer $created_at
 * @property integer $updated_at
 * @property EntityToFile $imageSrc
 */
class MemberItem extends ActiveRecord implements Translatable
{
    const SAVE_ATTRIBUTE_IMAGE = 'MemberItemImage';

    use TranslatedTrait;

    public $langModelClass = MemberItemLang::class;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%member_item}}';
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
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
