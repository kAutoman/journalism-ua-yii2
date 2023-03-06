<?php


namespace common\modules\config\domain\values;


use yii\bootstrap\Html;

class DateTimePicker extends Field
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
        $output .= \kartik\widgets\DateTimePicker::widget([
            'name' => $this->getName(),
            'value' => $this->getValue(),
            'options' => $options,
        ]);
        $output .= Html::tag('div', $this->getHint(), ['class' => 'hint-block']);
        $output .= $this->afterRender();

        return $output;
    }
}
