<?php

namespace common\modules\config\domain\values;

use yii\bootstrap\Html;
use yii\widgets\MaskedInput;

/**
 * Class PhoneInput
 *
 * @author Bogdan K. Fedun <delagics@gmail.com>
 */
class PhoneInput extends Field
{
    public function render(): string
    {
        $output = '';
        if (!$this->getIsDisplayable()) {
            return $output;
        }
        $output .= $this->beforeRender();
        $output .= Html::label($this->getLabel(), $this->getInputId());
        //$output .= Html::textInput($this->getName(), $this->getValue(), $this->preparedOptions());
        $output .= MaskedInput::widget([
            'name' => $this->getName(),
            'mask' => '999 999 99 99',
            'value' => $this->getValue(),
            'options' => $this->preparedOptions(),
        ]);
        $output .= Html::tag('div', $this->getHint(), ['class' => 'hint-block']);
        $output .= $this->afterRender();

        return $output;
    }
}
