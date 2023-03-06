<?php

namespace common\modules\config\domain\values;

use backend\widgets\Editor;
use yii\bootstrap\Html;
use yii\web\JsExpression;

/**
 * Class EditorInput
 *
 * @author Bogdan K. Fedun <delagics@gmail.com>
 */
class EditorInput extends Field
{
    public function render(): string
    {
        $output = '';
        if (!$this->getIsDisplayable()) {
            return $output;
        }
        $options = $this->preparedOptions();
        // Small fix to trigger textarea changed when leave editor.
        $clientOptions = ['setup' => new JsExpression('function (editor) {editor.on(\'change\', function () {tinymce.triggerSave();});}')];
        $clientOptions = merge(remove($options, 'clientOptions', []), $clientOptions);
        $output .= $this->beforeRender();
        $output .= Html::label($this->getLabel(), $this->getInputId());
        $output .= Editor::widget([
            'name' => $this->getName(),
            'value' => $this->getValue(),
            'options' => $options,
            'clientOptions' => $clientOptions,
        ]);

        $output .= Html::tag('div', $this->getHint(), ['class' => 'hint-block']);
        $output .= $this->afterRender();

        return $output;
    }
}
