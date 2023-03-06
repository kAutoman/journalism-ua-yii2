<?php

namespace common\models;

use common\components\model\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\behaviors\TranslatedBehavior;
use common\interfaces\Translatable;
use common\models\lang\MemberIconLang;
use lav45\translate\TranslatedTrait;

/**
 * This is the model class for table "{{%member_icon}}".
 *
 * @property integer $id
 * @property string $description
 * @property integer $published
 * @property integer $position
 * @property integer $created_at
 * @property integer $updated_at
 * @property EntityToFile $iconSrc
 */
class MemberIcon extends ActiveRecord implements Translatable
{
    const SAVE_ATTRIBUTE_ICON = 'MemberIconIcon';

    use TranslatedTrait;

    public $langModelClass = MemberIconLang::class;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%member_icon}}';
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
            'description',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
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
