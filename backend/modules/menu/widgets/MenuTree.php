<?php
/**
 * Created by PhpStorm.
 * User: anatolii
 * Date: 30.12.15
 * Time: 20:23
 */

namespace backend\modules\menu\widgets;

use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;


/**
 * Class MenuTree
 * @package backend\modules\menu\widgets
 */
class MenuTree extends MainMenu
{
    /** @inheritdoc */
    public function renderItem($item)
    {
        if (is_string($item)) {
            return $item;
        }
        if (!isset($item['label'])) {
            throw new InvalidConfigException("The 'label' option is required.");
        }
        $encodeLabel = isset($item['encode']) ? $item['encode'] : $this->encodeLabels;
        $label = $encodeLabel ? Html::encode($item['label']) : $item['label'];
        $options = ArrayHelper::getValue($item, 'options', []);
        $items = ArrayHelper::getValue($item, 'items');
        $url = ArrayHelper::getValue($item, 'url', '#');
        $linkOptions = ArrayHelper::getValue($item, 'linkOptions', []);

        if ($items !== null) {
            Html::addCssClass($options, ['widget' => 'dropdown']);
            if ($this->dropDownCaret !== '') {
                $label .= ' ' . $this->dropDownCaret;
            }
            if (is_array($items)) {
                $items = $this->renderDropdown($items, $item);
            }
        }

        Html::addCssClass($options, 'active');
        Html::addCssClass($options, 'open');

        return Html::tag('li', Html::a($label, $url, $linkOptions) . $items, $options);
    }
}
