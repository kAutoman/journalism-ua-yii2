<?php

namespace common\modules\config\domain\values;

use yii\helpers\Html;

/**
 * Class CtaTemplate
 * @package common\modules\config\domain\values
 * @author Andrew P. Kontseba <andjey.skinwalker@gmail.com>
 */
class CtaTemplate extends Field
{
    /**
     * Render field to string.
     * @return string
     */
    public function render(): string
    {
        $output = '';
        if (!$this->getIsDisplayable()) {
            return $output;
        }
        $options = $this->preparedOptions();
        $options = merge(
            $options,
            ['class' => 'form-control cta-viewer']
        );
        $output .= $this->beforeRender();
        $output .= Html::label($this->getLabel(), $this->getInputId());
        $output .= Html::dropDownList($this->getName(), $this->getValue(), remove($options, 'items', []), $options);
        $output .= Html::tag('div', $this->getHint(), ['class' => 'hint-block']);
        if (obtain('usePreview', $options, false)) {
            $output .= $this->getPreview($this->getValue());
        }
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

    private function getPreview($value)
    {
        return Html::tag('div', $value , ['class' => 'cta-viewer-wrap', 'data-id' => $value]);
    }
}
