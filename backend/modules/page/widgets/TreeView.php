<?php

namespace backend\modules\page\widgets;

use backend\modules\page\models\Page;
use Exception;
use execut\widget\TreeView as BaseTreeView;
use paulzi\nestedsets\NestedSetsBehavior;
use yii\helpers\Html;

/**
 * Class TreeView
 *
 * @package backend\modules\page\widgets
 */
class TreeView extends BaseTreeView
{
    const MOVE_DIRECTION_UP = 'up';
    const MOVE_DIRECTION_DOWN = 'down';
    const MOVE_DIRECTION_LEFT = 'left';
    const MOVE_DIRECTION_RIGHT = 'right';

    const INSERT_ROOT = 'root';
    const INSERT_BEGIN_LIST = 'begin';
    const INSERT_END_LIST = 'end';
    const INSERT_BEFORE_ITEM = 'before';
    const INSERT_AFTER_ITEM = 'after';
    const INSERT_CHILD = 'child';

    const TEMPLATE_BOXED = '<div class="tree-view-wrapper box box-primary">
    <div class="row tree-header box-header with-border">
        <div class="col-sm-6">
            <div class="tree-heading-container">{header}</div>
        </div>
        <div class="col-sm-6">
            {search}
        </div>
    </div>
    <div class="box-body tree-box-body">{tree}</div>
    <div class="box-footer">{controls}</div>
</div>';

    /**
     * @var Page | NestedSetsBehavior
     */
    public $model;

    public function run() {
        if ($this->size !== self::SIZE_NORMAL) {
            Html::addCssClass($this->options, $this->size);
        }

        if (!empty($this->options['id']) && $this->options['id'] !== $this->id) {
            throw new Exception('Set id directly to the widget via id config key instead redefine widget config options key');
        }

        if (strpos($this->template, '{tree}') === false) {
            throw new Exception('{tree} not found in widget template');
        }

        $parts = [
            '{tree}' => Html::tag('div', '', $this->options),
            '{header}' => $this->header,
            '{controls}' => $this->render('_controls', ['model' =>  $this->model])
        ];

        if (strpos($this->template, '{search}') !== false) {
            $parts['{search}'] = $this->renderSearchWidget();
        }

        echo Html::tag('div', strtr($this->template, $parts), $this->containerOptions);

        $this->clientOptions['data'] = $this->data;

        $this->registerWidget('treeview');
    }
}
