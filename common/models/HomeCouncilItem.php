<?php

namespace common\models;

use common\components\model\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\behaviors\TranslatedBehavior;
use common\interfaces\Translatable;
use common\models\lang\HomeCouncilItemLang;
use lav45\translate\TranslatedTrait;

/**
 * This is the model class for table "{{%home_council_item}}".
 *
 * @property integer $id
 * @property string $label
 * @property string $description
 * @property integer $published
 * @property integer $position
 * @property integer $created_at
 * @property integer $updated_at
 * @property EntityToFile $photoSrc
 */
class HomeCouncilItem extends ActiveRecord implements Translatable
{
    const SAVE_ATTRIBUTE_PHOTO = 'HomeCouncilItemPhoto';

    use TranslatedTrait;

    public $langModelClass = HomeCouncilItemLang::class;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%home_council_item}}';
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
            'description',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getPhotoSrc()
    {
        return $this->hasOne(EntityToFile::class, ['entity_model_id' => 'id'])
            ->andOnCondition([
                'photo.entity_model_name' => static::formName(),
                'photo.attribute' => static::SAVE_ATTRIBUTE_PHOTO
            ])
            ->alias('photo')
            ->orderBy('photo.position DESC');
    }
}
