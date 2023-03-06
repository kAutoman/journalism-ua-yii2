<?php

namespace common\models;

use common\components\model\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\behaviors\TranslatedBehavior;
use common\interfaces\Translatable;
use common\models\lang\JuryLang;
use lav45\translate\TranslatedTrait;

/**
 * This is the model class for table "{{%jury}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property integer $published
 * @property integer $position
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property EntityToFile $photoSrc
 */
class Jury extends ActiveRecord implements Translatable
{
    const SAVE_ATTRIBUTE_PHOTO = 'JuryPhoto';

    use TranslatedTrait;

    public $langModelClass = JuryLang::class;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%jury}}';
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
            'name',
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
