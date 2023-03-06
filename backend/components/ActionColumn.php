<?php
/**
 * 
 */

namespace backend\components;
use Yii;
use yii\helpers\Html;

/**
 * Class ActionColumn
 */
class ActionColumn extends \yii\grid\ActionColumn
{
    public $template = '{view} {update} {clone} {delete}';
    public $clone = true;

    public function init()
    {
        parent::init();
        $this->initButtons();
    }

    protected function initButtons()
    {
        if ($this->clone && !isset($this->buttons['clone'])) {
            $this->buttons['clone'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t('yii', 'Clone'),
                    'aria-label' => Yii::t('yii', 'Clone'),
                    'data-pjax' => '0',
                ], $this->buttonOptions);
                return Html::a('<span class="glyphicon glyphicon-duplicate"></span>', $url, $options);
            };
        }
    }

}
