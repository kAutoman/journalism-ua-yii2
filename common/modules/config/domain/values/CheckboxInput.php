<?php

namespace common\modules\config\domain\values;

use yii\bootstrap\Html;

/**
 * Class CheckboxInput
 *
 * @author Bogdan K. Fedun <delagics@gmail.com>
 */
class CheckboxInput extends Field
{
    public function render(): string
    {
        $output = '';
        if (!$this->getIsDisplayable()) {
            return $output;
        }

        $output .= $this->beforeRender();
        $checkbox = Html::hiddenInput($this->getName(), 0);
        $checkbox .= Html::checkbox($this->getName(), $this->getValue(), $this->preparedOptions());
        $output .= Html::label("$checkbox<span></span><strong>&nbsp;&nbsp;{$this->getLabel()}</strong>", $this->getInputId(), ['class' => 'css-input switch switch-warning']);
        $output .= Html::tag('div', $this->getHint(), ['class' => 'hint-block']);
        $output .= $this->afterRender();

        return $output;
    }
}
