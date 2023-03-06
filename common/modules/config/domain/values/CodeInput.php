<?php

namespace common\modules\config\domain\values;

use yii\bootstrap\Html;
use conquer\codemirror\CodemirrorAsset;
use conquer\codemirror\CodemirrorWidget;

/**
 * Class CodeInput
 *
 * @author Bogdan K. Fedun <delagics@gmail.com>
 */
class CodeInput extends Field
{
    public function render(): string
    {
        $output = '';
        if (!$this->getIsDisplayable()) {
            return $output;
        }
        $output .= $this->beforeRender();
        $output .= $this->getLabel() ? Html::label($this->getLabel(), $this->getInputId()) : '';
        //$output .= Html::textInput($this->getName(), $this->getValue(), $this->preparedOptions());
        $output .= CodemirrorWidget::widget([
            'name' => $this->getName(),
            'value' => $this->getValue(),
            'preset'=> 'javascript',
            'assets' => [
                CodemirrorAsset::ADDON_SEARCH,
                CodemirrorAsset::ADDON_DIALOG,
                CodemirrorAsset::ADDON_COMMENT,
                CodemirrorAsset::THEME_SOLARIZED,
                CodemirrorAsset::ADDON_SEARCHCURSOR,
                CodemirrorAsset::ADDON_EDIT_MATCHBRACKETS,
            ],
            'settings' => [
                'theme' => 'solarized',
                'lineNumbers' => true,
            ],
            'options' => ['rows' => 10],
        ]);
        $output .= Html::tag('div', $this->getHint(), ['class' => 'hint-block']);
        $output .= $this->afterRender();
        return $output;
    }
}
