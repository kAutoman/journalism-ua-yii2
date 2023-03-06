<?php


namespace common\widgets;


use common\components\TagLevel;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;

/**
 * Class InputTagWidget
 *
 * @package common\widgets
 * @deprecated
 */
class InputTagWidget extends InputWidget
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->options['label'] = false;
        $output = '';
        $output .= '<div class="row">';
        $output .= '<div class="col-sm-8">';
        $this->options['class'] = 'form-control';
        $output .= '<div class="form-group field-' . Html::getInputId($this->model,
                $this->attribute . '[label]') . '">';
        $output .= Html::label(\Yii::t('back/base', 'Label'),
            Html::getInputId($this->model, $this->attribute . '[label]'));
        $output .= Html::activeTextInput($this->model, $this->attribute . '[label]', $this->options);
        $output .= '</div>';
        $output .= '</div>';
        $output .= '<div class="col-sm-4">';
        $output .= '<div class="form-group field-' . Html::getInputId($this->model, $this->attribute . '[tag]') . '">';
        $output .= Html::label(\Yii::t('back/base', 'Tag'),
            Html::getInputId($this->model, $this->attribute . '[label]'));
        $output .= Html::activeDropDownList($this->model, $this->attribute . '[tag]', TagLevel::getTags(),
            $this->options);
        $output .= '</div>';
        $output .= '</div>';

        $output .= '</div>';

        echo $output;

        parent::run();
    }
}
