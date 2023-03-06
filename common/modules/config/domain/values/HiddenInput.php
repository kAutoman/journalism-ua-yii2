<?php

namespace common\modules\config\domain\values;

use yii\bootstrap\Html;

/**
 * Class HiddenInput
 *
 * @author Bogdan K. Fedun <delagics@gmail.com>
 */
class HiddenInput extends Field
{
    public function render(): string
    {
        $output = '';
        if (!$this->getIsDisplayable()) {
            return $output;
        }
        $output .= $this->beforeRender();
        $output .= Html::hiddenInput($this->getName(), $this->getValue(), $this->preparedOptions());
        $output .= $this->afterRender();

        return $output;
    }
}
