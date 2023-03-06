<?php

namespace common\modules\config\domain\values;

use dosamigos\selectize\SelectizeDropDownList;
use yii\helpers\Json;
use yii\bootstrap\Html;
use dosamigos\selectize\SelectizeTextInput;

/**
 * Class SelectizeInput
 * @package common\modules\config\domain\values
 * @author Andrew P. Kontseba <andjey.skinwalker@gmail.com>
 */
class SelectizeInput extends Field
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
        $value = Json::decode($this->getValue(), true);
        $items = obtain('items', $options) ?? [];
        if (is_array($value)) {
            foreach ($value as $one) {
                $eventsnew[$one] = $items[$one] ?? [];
                unset($items[$one]);
                $items[$one] = $eventsnew[$one];
            }
        }
        $optionsInput = obtain('options', $options);
        $optionsInput['id'] = $this->getInputId();
        $output .= SelectizeDropDownList::widget([
            'name' => $this->getName(),
            'options' => $optionsInput,
            'items' => $items,
            'value' => $value,
            'clientOptions' => obtain('clientOptions', $options),
        ]);
        $output .= $this->afterRender();

        return $output;
    }
}
