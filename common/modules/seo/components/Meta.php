<?php

namespace common\modules\seo\components;

use common\components\model\ActiveRecord;
use common\components\model\DynamicModel;
use common\modules\seo\models\MetaTags;
use yii\base\InvalidConfigException;
use yii\base\UnknownPropertyException;
use yii\helpers\ArrayHelper;
use yii\validators\Validator;

/**
 * Class MetaTagsRepository
 *
 * @package common\modules\seo\repositories
 */
class Meta extends DynamicModel
{

    private $tags;

    /**
     * Constructors.
     * @param array $attributes the dynamic attributes (name-value pairs, or names).
     * @param array $rules the dynamic attributes validation rules.
     * @param array $labels the dynamic attributes labels (name-value pairs).
     * @param array $config the configuration array to be applied to this object.
     */
    public function __construct(array $attributes = [], array $rules = [], array $labels = [], $config = [])
    {
        $metaTags = MetaTags::find()->orderBy('position')->all();
        $this->setTags($metaTags);

        $attributes = ArrayHelper::getColumn($metaTags, 'name');

        return parent::__construct($attributes, $rules, $labels, $config);
    }

    /**
     * @return MetaTags[]
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param mixed $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }
}
