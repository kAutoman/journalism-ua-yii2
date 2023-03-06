<?php

namespace common\modules\config\domain\values;

use yii\bootstrap\Html;

/**
 * Class RadioInput
 *
 * @author Bogdan K. Fedun <delagics@gmail.com>
 */
class RadioInput extends Field
{
    public function render(): string
    {
        $output = '';
        if (!$this->getIsDisplayable()) {
            return $output;
        }
        $output .= $this->beforeRender();
        $output .= Html::label($this->getLabel(), $this->getInputId());
        $output .= Html::radio($this->getName(), $this->getValue(), $this->preparedOptions());
        $output .= Html::tag('div', $this->getHint(), ['class' => 'hint-block']);
        $output .= $this->afterRender();

        return $output;
    }
}
