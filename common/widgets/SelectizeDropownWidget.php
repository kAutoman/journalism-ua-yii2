<?php


namespace common\widgets;


use dosamigos\selectize\InputWidget;
use yii\helpers\Html;

class SelectizeDropownWidget extends InputWidget
{
    /**
     * @var array
     */
    public $items = [];

    /**
     * @inheritdoc
     */
    public function run()
    {

        if (is_array($this->value)) {
            foreach ($this->value as $item) {
                $items[$item] = $this->items[$item] ?? [];
                unset($this->items[$item]);
                $this->items[$item] = $items[$item];
            }
        }

        if ($this->hasModel()) {
            echo Html::activeDropDownList($this->model, $this->attribute, $this->items, $this->options);
        } else {
            echo Html::dropDownList($this->name, $this->value, $this->items, $this->options);
        }

        parent::run();
    }

}
