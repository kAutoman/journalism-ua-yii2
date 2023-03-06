<?php

namespace backend\components\grid;

use Yii;
use yii\grid\ActionColumn;
use yii\helpers\Html;

/**
 * Class StylingActionColumn
 *
 * @package backend\components\grid
 */
class StylingActionColumn extends ActionColumn
{
    /**
     * @var string
     */
    public $template = '{update}{delete}';

    /**
     * Initializes the default button rendering callbacks.
     */
    protected function initDefaultButtons()
    {
        $this->initDefaultButton('rating', 'star');
        $this->initDefaultButton('view', 'eye');
        $this->initDefaultButton('update', 'pencil');
        $this->initDefaultButton('delete', 'times', [
            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
            'data-method' => 'post',
        ], true);
    }

    /**
     * @inheritdoc
     */
    protected function initDefaultButton($name, $iconName, $additionalOptions = [], $isDanger = false)
    {
        if (!isset($this->buttons[$name]) && strpos($this->template, '{' . $name . '}') !== false) {
            $this->buttons[$name] = function ($url, $model, $key) use ($name, $iconName, $additionalOptions, $isDanger) {
                switch ($name) {
                    case 'rating':
                        $title = Yii::t('yii', 'Rating');
                        break;
                    case 'view':
                        $title = Yii::t('yii', 'View');
                        break;
                    case 'update':
                        $title = Yii::t('yii', 'Update');
                        break;
                    case 'delete':
                        $title = Yii::t('yii', 'Delete');
                        break;
                    default:
                        $title = ucfirst($name);
                }
                $options = array_merge([
                    'title' => $title,
                    'aria-label' => $title,
                    'data-pjax' => '0',
                    'class' => 'btn btn-xs btn-default'
                ], $additionalOptions, $this->buttonOptions);

                if ($isDanger){
                    $icon = Html::tag('span', '', ['class' => "text-danger fa fa-$iconName"]);
                }
                else{
                    $icon = Html::tag('span', '', ['class' => "fa fa-$iconName"]);
                }

                return Html::a($icon, $url, $options);
            };
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $buttons = preg_replace_callback('/\\{([\w\-\/]+)\\}/', function ($matches) use ($model, $key, $index) {
            $name = $matches[1];

            if (isset($this->visibleButtons[$name])) {
                $isVisible = $this->visibleButtons[$name] instanceof \Closure
                    ? call_user_func($this->visibleButtons[$name], $model, $key, $index)
                    : $this->visibleButtons[$name];
            } else {
                $isVisible = true;
            }

            if ($isVisible && isset($this->buttons[$name])) {
                $url = $this->createUrl($name, $model, $key, $index);
                return call_user_func($this->buttons[$name], $url, $model, $key);
            }

            return '';
        }, $this->template);

        return Html::tag('div', $buttons, ['class' => 'btn-group-vertical', 'role' => 'group']);
    }
}
