<?php

namespace backend\helpers;

use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Class CheckboxHelper
 */
class CheckboxHelper
{
    /**
     * @param $model
     * @param $attribute
     * @param $key
     * @param string $action
     * @return string
     */
    public static function renderCheckboxCell($model, $attribute, $key, $action = 'change')
    {
        /** @var \yii\db\ActiveRecord $model */
        return Html::beginTag('div', ['id' => 'checkbox-wrapper-' . $attribute . '-' . $key]) .
            Html::checkbox(null, (int)$model->{$attribute}, [
                'data' => [
                    'href' => Url::to([
                        $action,
                        'id' => $model->getPrimaryKey(true),
                        'attribute' => $attribute,
                        'value' => (int)!$model->{$attribute},
                        'key' => $key,
                    ]),
                    'ajax' => true,
                ],
            ]) .
            Html::endTag('div');
    }
}
