<?php

namespace common\modules\seo\models;

use backend\components\FormBuilder;
use common\components\model\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%meta_tags}}".
 *
 * @property integer $id
 * @property string $label
 * @property string $name
 * @property integer $type
 * @property integer $position
 */
class MetaTags extends ActiveRecord
{
    const TYPE_TEXT = 0;
    const TYPE_TEXTAREA = 1;
    const TYPE_IMAGE = 2;
    const TYPE_CHECKBOX = 3;
    const TYPE_CODE = 4;

    const OPEN_GRAPH_IMAGE_ATTRIBUTE = 'og_image_attribute';

    public static $tags = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%meta_tags}}';
    }

    public static function formTypeMapping(): array
    {
        return [
            self::TYPE_TEXT => FormBuilder::INPUT_TEXT,
            self::TYPE_TEXTAREA => FormBuilder::INPUT_TEXTAREA,
            self::TYPE_IMAGE => FormBuilder::INPUT_WIDGET,
            self::TYPE_CHECKBOX => FormBuilder::INPUT_CHECKBOX,
            self::TYPE_CODE => FormBuilder::INPUT_TEXTAREA, // @todo change to code input
        ];
    }

    /**
     * @return string|null
     */
    public function getFormType(): ?string
    {
        return self::formTypeMapping()[$this->type] ?? null;
    }

    /**
     * @return array
     */
    public static function getMetaTagsList(): array
    {
        return ArrayHelper::getColumn(self::getAllTags(), 'name');
    }

    /**
     * @return array|MetaTags[]
     */
    public static function getAllTags()
    {
        if (empty(self::$tags)) {
            self::$tags = self::find()->orderBy('position')->all();
        }

        return self::$tags;
    }
}
