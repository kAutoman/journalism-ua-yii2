<?php
/**
 * Created by PhpStorm.
 * User: viktor
 * Date: 21.09.18
 * Time: 14:07
 */

namespace common\modules\config\domain\values;

use yii\helpers\Json;
use yii\bootstrap\Html;
use unclead\multipleinput\MultipleInput;

/**
 * Class MultipleColumn
 * @package common\modules\config\domain\values
 */
class MultipleColumn extends Field
{
    /**
     * @return string
     * @throws \Exception
     */
    public function render(): string
    {
        $output = '';
        if (!$this->getIsDisplayable()) {
            return $output;
        }
        $options = $this->preparedOptions();

        $output .= $this->beforeRender();
        $output .= Html::label($this->getLabel(), $this->getInputId());
        unset($options['placeholder']);
        $options['name'] = $this->getName();
        $options['value'] = Json::decode($this->getValue(), true);

        $output .= MultipleInput::widget($options);
        $output .= $this->afterRender();

        return $output;
    }


}
