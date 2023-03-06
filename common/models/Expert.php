<?php

namespace common\models;

use common\components\model\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\behaviors\TranslatedBehavior;
use common\interfaces\Translatable;
use common\models\lang\ExpertLang;
use lav45\translate\TranslatedTrait;

/**
 * This is the model class for table "{{%expert}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $staff
 * @property integer $published
 * @property integer $position
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property EntityToFile $photoSrc
 */
class Expert extends ActiveRecord implements Translatable
{
    const SAVE_ATTRIBUTE_PHOTO = 'ExpertPhoto';

    use TranslatedTrait;

    public $langModelClass = ExpertLang::class;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%expert}}';
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
            'staff',
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
