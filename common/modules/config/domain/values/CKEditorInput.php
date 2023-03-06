<?php

namespace common\modules\config\domain\values;

use backend\components\CKEditorContent;
use backend\widgets\Editor;
use yii\bootstrap\Html;
use yii\web\JsExpression;

/**
 * Class CKEditorInput
 * @package common\modules\config\domain\values
 * @author Andrew P. Kontseba <andjey.skinwalker@gmail.com>
 */
class CKEditorInput extends Field
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
        $output .= CKEditorContent::widget([
            'name' => $this->getName(),
            'value' => $this->getValue(),
            'options' => $options,
        ]);
        $output .= Html::tag('div', $this->getHint(), ['class' => 'hint-block']);
        $output .= $this->afterRender();

        return $output;
    }
}
