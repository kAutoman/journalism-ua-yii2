<?php

namespace common\modules\faq\models;

use yii\helpers\ArrayHelper;
use yii\behaviors\TimestampBehavior;
use lav45\translate\TranslatedTrait;
use common\interfaces\Translatable;
use common\behaviors\TranslatedBehavior;
use common\components\model\ActiveRecord;
use common\modules\faq\models\lang\FaqCategoryLang;

/**
 * This is the model class for table "{{%faq_category}}".
 *
 * @property integer $id
 * @property string $alias
 * @property integer $published
 * @property integer $position
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Faq[] $faqs
 */
class FaqCategory extends ActiveRecord implements Translatable
{
    use TranslatedTrait;

    const COMMON_CATEGORY_ID = 1;

    public $langModelClass = FaqCategoryLang::class;

    public static $permanentRecords = [
        self::COMMON_CATEGORY_ID
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%faq_category}}';
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

    public function getFaqs()
    {
        return $this->hasMany(Faq::class, ['category_id' => 'id']);
    }

    /**
     * @param bool $onlyPublished
     * @return array
     */
    public static function getList(bool $onlyPublished = false): array
    {
        $query = self::find()->orderBy('position');
        if ($onlyPublished) {
            $query->isPublished();
        }

        return ArrayHelper::map($query->all(), 'id', 'label');
    }

    /**
     * @return bool
     */
    public function isPermanent(): bool
    {
        return in_array($this->id, self::$permanentRecords);
    }
}
