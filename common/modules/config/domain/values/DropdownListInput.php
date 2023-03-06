<?php

namespace common\modules\config\domain\values;

use yii\bootstrap\Html;

/**
 * Class DropdownListInput
 *
 * @author Bogdan K. Fedun <delagics@gmail.com>
 */
class DropdownListInput extends Field
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
        $output .= Html::dropDownList($this->getName(), $this->getValue(), remove($options, 'items', []), $options);
        $output .= Html::tag('div', $this->getHint(), ['class' => 'hint-block']);
        $output .= $this->afterRender();

        return $output;
    }

    public function getDefaultOptions()
    {
        return [
            'id' => $this->getInputId(),
            'class' => 'form-control',
        ];
    }
}
