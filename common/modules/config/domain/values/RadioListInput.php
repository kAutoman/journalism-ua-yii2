<?php

namespace common\modules\config\domain\values;

use yii\bootstrap\Html;

/**
 * Class RadioListInput
 *
 * @author Bogdan K. Fedun <delagics@gmail.com>
 */
class RadioListInput extends Field
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
        $output .= Html::radioList($this->getName(), $this->getValue(), remove($options, 'items', []), $options);
        $output .= Html::tag('div', $this->getHint(), ['class' => 'hint-block']);
        $output .= $this->afterRender();

        return $output;
    }

    public function getDefaultOptions()
    {
        return [
            'id' => $this->getInputId(),
        ];
    }
}
