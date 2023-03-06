<?php

namespace common\models;

use common\components\model\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\behaviors\TranslatedBehavior;
use common\interfaces\Translatable;
use common\models\lang\HomePartnerItemLang;
use lav45\translate\TranslatedTrait;

/**
 * This is the model class for table "{{%home_partner_item}}".
 *
 * @property integer $id
 * @property string $label
 * @property string $link
 * @property integer $published
 * @property integer $position
 * @property integer $created_at
 * @property integer $updated_at
 * @property EntityToFile $logoSrc
 */
class HomePartnerItem extends ActiveRecord implements Translatable
{
    const SAVE_ATTRIBUTE_LOGO = 'HomePartnerItemLogo';

    use TranslatedTrait;

    public $langModelClass = HomePartnerItemLang::class;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%home_partner_item}}';
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getLogoSrc()
    {
        return $this->hasOne(EntityToFile::class, ['entity_model_id' => 'id'])
            ->andOnCondition([
                'logo.entity_model_name' => static::formName(),
                'logo.attribute' => static::SAVE_ATTRIBUTE_LOGO
            ])
            ->alias('logo')
            ->orderBy('logo.position DESC');
    }
}
