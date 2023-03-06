<?php

namespace common\models;

use common\components\model\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\behaviors\TranslatedBehavior;
use common\interfaces\Translatable;
use common\models\lang\MemberTimelineLang;
use lav45\translate\TranslatedTrait;

/**
 * This is the model class for table "{{%member_timeline}}".
 *
 * @property integer $id
 * @property string $label
 * @property string $content
 * @property string $date
 * @property integer $published
 * @property integer $position
 * @property integer $created_at
 * @property integer $updated_at
 */
class MemberTimeline extends ActiveRecord implements Translatable
{
    use TranslatedTrait;

    public $langModelClass = MemberTimelineLang::class;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%member_timeline}}';
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
            'date',
        ];
    }
}
