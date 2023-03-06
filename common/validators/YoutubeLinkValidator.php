<?php

namespace common\validators;

use yii\validators\StringValidator;
use common\components\model\ActiveRecord;

/**
 * Class YoutubeLinkValidator
 *
 * @package common\validators
 */
class YoutubeLinkValidator extends StringValidator
{
    /**
     * @param ActiveRecord $model
     * @param string $attribute
     * @return bool|void
     */
    public function validateAttribute($model, $attribute)
    {
        parent::validateAttribute($model, $attribute);
        $matches = [
            preg_match("/(www.youtube.com\/watch.v=+)([^=]+)/i", $model->$attribute),
            preg_match("/(youtu.be\/+)([^\/]+)/i", $model->$attribute),
            preg_match("/(www.youtube.com\/embed\/+)([^\/]+)/i", $model->$attribute),
        ];

        if (!in_array(1, $matches)) {
            $this->addError($model, $attribute, bt('Only YouTube links are accepted'));
        }
    }
}
