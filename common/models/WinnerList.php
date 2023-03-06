<?php

namespace common\models;

use common\components\model\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\behaviors\TranslatedBehavior;
use common\interfaces\Translatable;
use common\models\lang\WinnerListLang;
use lav45\translate\TranslatedTrait;

/**
 * This is the model class for table "{{%winner_list}}".
 *
 * @property integer $id
 * @property integer $member_item_id
 * @property string $name
 * @property string $publication_label
 * @property string $publication_link
 * @property integer $published
 * @property integer $position
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property MemberItem $memberItem
 *
 * @property EntityToFile $imageSrc
 * @property EntityToFile $fileSrc
 */
class WinnerList extends ActiveRecord implements Translatable
{
    const SAVE_ATTRIBUTE_IMAGE = 'WinnerListImage';
    const SAVE_ATTRIBUTE_FILE = 'WinnerListFile';

    use TranslatedTrait;

    public $langModelClass = WinnerListLang::class;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%winner_list}}';
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
            'publication_label',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMemberItem()
    {
        return $this->hasOne(MemberItem::class, ['id' => 'member_item_id']);
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

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getFileSrc()
    {
        return $this->hasOne(EntityToFile::class, ['entity_model_id' => 'id'])
            ->andOnCondition([
                'file.entity_model_name' => static::formName(),
                'file.attribute' => static::SAVE_ATTRIBUTE_FILE
            ])
            ->alias('file')
            ->orderBy('file.position DESC');
    }
}
