<?php

namespace common\models;

use common\components\model\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\behaviors\TranslatedBehavior;
use common\interfaces\Translatable;
use common\models\lang\CategoryNewsLang;
use lav45\translate\TranslatedTrait;

/**
 * This is the model class for table "{{%category_news}}".
 *
 * @property integer $id
 * @property string $alias
 * @property integer $published
 * @property integer $position
 * @property integer $created_at
 * @property integer $updated_at
 */
class CategoryNews extends ActiveRecord implements Translatable
{

    use TranslatedTrait;

    public $langModelClass = CategoryNewsLang::class;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%category_news}}';
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
        ];
    }
}
