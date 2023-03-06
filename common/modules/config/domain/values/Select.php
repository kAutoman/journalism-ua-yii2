<?php

namespace common\modules\config\domain\values;

use kartik\widgets\Select2;
use yii\bootstrap\Html;
use yii\helpers\Json;

/**
 * Class Select
 */
class Select extends Field
{
    public function render(): string
    {
        $output = '';
        if (!$this->getIsDisplayable()) {
            return $output;
        }

        $options = $this->preparedOptions();
        $output .= $this->beforeRender();
        $output .= Html::label($this->getLabel(), $this->getInputId());
        $output .= Select2::widget([
            'name' => $this->getName(),
            'value' => Json::decode($this->getValue()),
            'data' => $options['data'] ?? [],
            'options' => $options
        ]);
        $output .= $this->afterRender();

        return $output;
    }
}
