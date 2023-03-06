<?php

namespace common\modules\faq\models;

use common\helpers\UrlHelper;
use yii\behaviors\TimestampBehavior;
use lav45\translate\TranslatedTrait;
use common\interfaces\Translatable;
use common\behaviors\TranslatedBehavior;
use common\components\model\ActiveRecord;
use common\modules\faq\models\lang\FaqLang;

/**
 * This is the model class for table "{{%faq}}".
 *
 * @property integer $id
 * @property integer $category_id
 * @property integer $published
 * @property integer $position
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property string $question
 * @property string $answer
 *
 * @property FaqCategory $category
 */
class Faq extends ActiveRecord implements Translatable
{

    use TranslatedTrait;

    public $langModelClass = FaqLang::class;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%faq}}';
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
            'question',
            'answer',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(FaqCategory::class, ['id' => 'category_id']);
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public static function getItemsListUrl($params = [])
    {
        return UrlHelper::createUrl('/faq/faq/index', $params);
    }
}
