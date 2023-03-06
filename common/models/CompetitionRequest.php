<?php

namespace common\models;

use backend\modules\request\models\CompetitionRequestRating;
use common\components\model\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%competition_request}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $gender
 * @property integer $gender_id
 * @property string $age
 * @property string $city
 * @property string $position
 * @property string $company_name
 * @property string $phone
 * @property string $experience
 * @property string $other_name
 * @property string $material_label
 * @property string $material_type
 * @property integer $material_type_id
 * @property string $program_label
 * @property string $program_published_date
 * @property string $program_link
 * @property string $nomination
 * @property integer $nomination_id
 * @property string $argument
 * @property string $awards
 * @property integer $status
 * @property string $moderator_comment
 * @property string $email_message
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property MemberItem $nominationItem
 * @property CompetitionRequestRating[] $globalRating
 */
class CompetitionRequest extends ActiveRecord
{
    const GENDER_NONE = 0;
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;
    const GENDER_OTHER = 3;

    const TYPE_VIDEO = 1;
    const TYPE_AUDIO = 2;
    const TYPE_TEXT = 3;

    const STATUS_NONE = 0;
    const STATUS_REJECT = 1;
    const STATUS_ACCEPT = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%competition_request}}';
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNominationItem()
    {
        return $this->hasOne(MemberItem::class, ['id' => 'nomination_id']);
    }

    /**
     * @return array
     */
    public static function getGenders(): array
    {
        return [
            self::GENDER_MALE => bt('Male', 'form'),
            self::GENDER_FEMALE => bt('Female', 'form'),
            self::GENDER_NONE => bt('I will not answer', 'form'),
            self::GENDER_OTHER => bt('Other', 'form'),
        ];
    }

    /**
     * @return string|null
     */
    public function getGender(): ?string
    {
        return obtain($this->gender_id, self::getGenders(), $this->gender);
    }

    /**
     * @return array
     */
    public static function getMaterialTypes(): array
    {
        return [
            self::TYPE_VIDEO => bt('Video', 'form'),
            self::TYPE_AUDIO => bt('Audio', 'form'),
            self::TYPE_TEXT => bt('Text', 'form'),
        ];
    }

    /**
     * @return string|null
     */
    public function getMaterialType(): ?string
    {
        return obtain($this->material_type_id, self::getMaterialTypes(), $this->material_type);
    }

    /**
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_NONE => bt('None', 'form'),
            self::STATUS_REJECT => bt('Reject', 'form'),
            self::STATUS_ACCEPT => bt('Accept', 'form'),
        ];
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return obtain($this->status, self::getStatuses());
    }

    /**
     * @return array
     */
    public static function getNominations(): array
    {
        /** @var MemberItem[] $models */
        $models = MemberItem::find()
            ->isPublished()
            ->orderBy([
                'position' => SORT_ASC
            ])
            ->all();

        return map($models, 'id', 'label');
    }

    /**
     * @return string|null
     */
    public function getNomination(): ?string
    {
        return $this->nominationItem ? $this->nominationItem->label : $this->nomination;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGlobalRating()
    {
        return $this->hasMany(CompetitionRequestRating::class, ['request_id' => 'id']);
    }
}
