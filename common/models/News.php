<?php

namespace common\models;

use common\components\model\ActiveRecord;
use common\modules\builder\behaviors\BuilderBehavior;
use common\modules\builder\models\SampleModel;
use yii\behaviors\TimestampBehavior;
use common\behaviors\TranslatedBehavior;
use common\interfaces\Translatable;
use common\models\lang\NewsLang;
use lav45\translate\TranslatedTrait;

/**
 * This is the model class for table "{{%news}}".
 *
 * @property integer $id
 * @property string $alias
 * @property integer $published
 * @property integer $position
 * @property integer $publication_date
 * @property integer $created_at
 * @property integer $updated_at
 */
class News extends ActiveRecord implements Translatable
{

    use TranslatedTrait;

    public $langModelClass = NewsLang::class;

    public $builderContent;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%news}}';
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
            'builder' => [
                'class' => BuilderBehavior::class,
                'attribute' => 'builderContent',
                'models' => [
                    SampleModel::class
                ]
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

    public function getCategoriesIds()
    {
        return $this->hasMany(NewsToCategory::class,
            ['news_id' => 'id'])->orderBy(['id' => SORT_ASC])->indexBy('category_id');
    }
}
