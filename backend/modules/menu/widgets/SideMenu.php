<?php

namespace backend\modules\menu\widgets;

use kartik\sidenav\SideNav;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use Yii;

class SideMenu extends SideNav
{
    public $activeFound = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->items = require(Yii::getAlias('@backend') . '/config/menu-items.php');

        $this->heading = false;
        $this->options['class'] = 'nav-main';

        $this->trigger('init');
    }

    /**
     * @return string
     */
    public function run()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->renderMenu();
        }
    }

    /**
     * @param array $item
     * @return string
     */
    protected function renderItem($item)
    {
        $activeClass = $item['active'] ? 'active' : '';
        $this->validateItems($item);
        $template = '<a class="' . $activeClass . '" href="{url}" >{icon}{label}</a>';
        $url = Url::to(ArrayHelper::getValue($item, 'url', '#'));
        if (empty($item['top'])) {
            if (!empty($item['items'])) {
                $template = isset($item['template']) ? $item['template'] : '<a href="{url}" class="' . $activeClass . ' nav-submenu" data-toggle="nav-submenu">{icon}<span class="sidebar-mini-hide">{label}</span></a>';
            }
        }
        $icon = empty($item['icon']) ? '' : $item['icon'];

        unset($item['icon'], $item['top']);
        return strtr($template, [
            '{url}' => $url,
            '{label}' => $item['label'],
            '{icon}' => $icon
        ]);
    }

    protected function checkActive($item, $key)
    {
        if (($key === 'active') and ($item === true)) {
            $this->activeFound = true;
        }
    }

    protected function renderItems($items)
    {
        $n = count($items);
        $lines = [];
        foreach ($items as $i => $item) {
            $options = array_merge($this->itemOptions, ArrayHelper::getValue($item, 'options', []));

            $this->activeFound = false;
            array_walk_recursive($item, 'self::checkActive');

            if ($this->activeFound) {
                $options['class'] = 'open';
            }

            $tag = ArrayHelper::remove($options, 'tag', 'li');
            $class = [];

            if ($item['active']) {
                $class[] = $this->activeCssClass;
            }
            if ($i === 0 && $this->firstItemCssClass !== null) {
                $class[] = $this->firstItemCssClass;
            }
            if ($i === $n - 1 && $this->lastItemCssClass !== null) {
                $class[] = $this->lastItemCssClass;
            }
            Html::addCssClass($options, $class);

            $menu = $this->renderItem($item);
            if (!empty($item['items'])) {
                $submenuTemplate = ArrayHelper::getValue($item, 'submenuTemplate', $this->submenuTemplate);
                $menu .= strtr($submenuTemplate, [
                    '{items}' => $this->renderItems($item['items']),
                ]);
            }
            $lines[] = Html::tag($tag, $menu, $options);
        }
        return implode("\n", $lines);
    }

    protected function renderMenu()
    {
        if ($this->route === null && Yii::$app->controller !== null) {
            $this->route = Yii::$app->controller->getRoute();
        }
        if ($this->params === null) {
            $this->params = $_GET;
        }
        $items = $this->normalizeItems($this->items, $hasActiveChild);
        $options = $this->options;
        $tag = ArrayHelper::remove($options, 'tag', 'ul');

        $this->activeFound = false;
        array_walk_recursive($items, 'self::checkActive');

        $session = Yii::$app->session;
        if (!$this->activeFound) {
            $items = $items;
        }

        //$session->set('side_menu', $items);

        return Html::tag($tag, $this->renderItems($items), $options);
    }

    protected function isItemActive($item)
    {
        if (isset($item['url']) && is_array($item['url']) && isset($item['url'][0])) {
            $route = Yii::getAlias($item['url'][0]);
            $configGet = Yii::$app->request->get('aggregate') ?? false;
            if ($route[0] !== '/' && Yii::$app->controller) {
                $route = Yii::$app->controller->module->getUniqueId() . '/' . $route;
            }
            if (ltrim($route, '/') !== $this->route && ltrim($route, '/') !== 'config/'.$configGet) {
                return false;
            }
            unset($item['url']['#']);
            if (count($item['url']) > 1) {
                $params = $item['url'];
                unset($params[0]);
                foreach ($params as $name => $value) {
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
