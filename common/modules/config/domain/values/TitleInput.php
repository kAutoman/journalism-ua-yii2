<?php


namespace common\modules\config\domain\values;


use common\components\TagLevel;
use yii\bootstrap\Html;
use yii\helpers\Json;

class TitleInput extends Field
{
    public function render(): string
    {
        $output = '';
        if (!$this->getIsDisplayable()) {
            return $output;
        }
        $value = Json::decode($this->getValue()) ?? [];
        $output .= $this->beforeRender();
        $output .= '<div class="row">';
        $output .= '<div class="col-sm-8">';

        $output .= Html::label($this->getLabel(), $this->getInputId());
        $output .= Html::textInput($this->getName() . '[label]', $value[0] ?? '', $this->preparedOptions());
        $output .= Html::tag('div', $this->getHint(), ['class' => 'hint-block']);
        $output .= '</div>';
        $output .= '<div class="col-sm-4">';
        $output .= Html::label('H tag', $this->getInputId());
        $output .= Html::dropDownList($this->getName() . '[tag]', $value[1] ?? 2, TagLevel::getTags(),
            $this->preparedOptions());
        $output .= Html::tag('div', $this->getHint(), ['class' => 'hint-block']);
        $output .= '</div>';
        $output .= '</div>';
        $output .= $this->afterRender();

        return $output;
    }
}
