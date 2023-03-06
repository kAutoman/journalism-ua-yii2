<?php
namespace backend\widgets;

use dmstr\widgets\Menu as BaseMenu;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

class Menu extends BaseMenu
{
    public $defaultIconHtml = '<i class="fa fa-edit"></i> ';
    public $linkTemplate = '<a href="{url}" {linkOptions}>{icon} {label}</a>';
    public $encodeLabels = false;

    /**
     * @var string
     */
    public static $iconClassPrefix = 'fa fa-';

    protected function renderItem($item)
    {
        if (isset($item['items'])) {
            $labelTemplate = '<a href="{url}" {linkOptions}>{icon} {label} <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>';
            $linkTemplate = '<a href="{url}" {linkOptions}>{icon} {label} <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>';
        } else {
            $labelTemplate = $this->labelTemplate;
            $linkTemplate = $this->linkTemplate;
        }

        $linkOptions = Html::renderTagAttributes(ArrayHelper::getValue($item, 'linkOptions', []));

        if (isset($item['url'])) {
            $template = ArrayHelper::getValue($item, 'template', $linkTemplate);
            $replace = !empty($item['icon']) ? [
                '{url}' => Url::to($item['url']),
                '{label}' => '<span>' . $item['label'] . '</span>',
                '{icon}' => '<i class="' . self::$iconClassPrefix . $item['icon'] . '"></i> ',
                '{linkOptions}' => $linkOptions
            ] : [
                '{url}' => Url::to($item['url']),
                '{label}' => '<span>' . $item['label'] . '</span>',
                '{icon}' => $this->defaultIconHtml,
                '{linkOptions}' => $linkOptions
            ];
            return strtr($template, $replace);
        } else {
            $template = ArrayHelper::getValue($item, 'template', $labelTemplate);
            $replace = !empty($item['icon']) ? [
                '{label}' => '<span>' . $item['label'] . '</span>',
                '{icon}' => '<i class="' . self::$iconClassPrefix . $item['icon'] . '"></i> ',
                '{linkOptions}' => $linkOptions
            ] : [
                '{label}' => '<span>' . $item['label'] . '</span>',
                '{icon}' => $this->defaultIconHtml,
                '{linkOptions}' => $linkOptions
            ];
            return strtr($template, $replace);
        }
    }

    /**
     * Checks whether a menu item is active.
     * This is done by checking if [[route]] and [[params]] match that specified in the `url` option of the menu item.
     * When the `url` option of a menu item is specified in terms of an array, its first element is treated
     * as the route for the item and the rest of the elements are the associated parameters.
     * Only when its route and parameters match [[route]] and [[params]], respectively, will a menu item
     * be considered active.
     * @param array $item the menu item to be checked
     * @return boolean whether the menu item is active
     */
    protected function isItemActive($item)
    {
        if (isset($item['url']) && is_array($item['url']) && isset($item['url'][0])) {
            $routeData = explode('/', $item['url'][0]);
            $module = \Yii::$app->controller->module->id;
            $controller = \Yii::$app->controller->id;
            $action = \Yii::$app->controller->action->id;

            $configGet = Yii::$app->request->get('aggregate', false);
            if ($configGet && $configGet === end($routeData)) {
                return true;
            }

            if ($routeData[1] != $module || $routeData[2] != $controller || $routeData[3] != $action) {
                return false;
            }
            unset($item['url']['#']);
            if (count($item['url']) > 1) {
                foreach (array_splice($item['url'], 1) as $name => $value) {
                    if ($value !== null && (!isset($this->params[$name]) || $this->params[$name] != $value)) {
                        return false;
                    }
                }
            }
            return true;
        }
        return false;
    }
}
