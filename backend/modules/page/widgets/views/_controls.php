<?php

use yii\helpers\Html;
use yii\bootstrap\Dropdown;
use backend\modules\page\models\Page;
use paulzi\nestedsets\NestedSetsBehavior;
use backend\modules\page\widgets\TreeView;

/**
 * @var Page | NestedSetsBehavior $model
 */

$currentModelId = request()->get('id', $model->id);
$module = app()->getModule('page');
?>

<div class="btn-group" role="group" aria-label="">
    <?php if (app()->params['editPage'] ?? false): ?>

        <div class="btn-group" role="group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-plus"></i>
                Add new
                <span class="caret"></span>
            </button>
            <?= Dropdown::widget([
                'encodeLabels' => false,
                'items' => [
                    [
                        'label' => '<i class="fa fa-tree"></i>' . ' Root',
                        'visible' => !$module->singleRoot,
                        'url' => ['/page/page/create', 'id' => $currentModelId, 'location' => TreeView::INSERT_ROOT]
                    ],
                    [
                        'label' => '<i class="fa fa fa-angle-double-right"></i>' . ' Child element',
                        'visible' => $model->child_allowed && $module->maxDepth > $model->depth,
                        'url' => ['/page/page/create', 'id' => $currentModelId, 'location' => TreeView::INSERT_CHILD]
                    ],
                    '<li role="separator" class="divider"></li>',
                    [
                        'label' => '<i class="fa fa-angle-double-up"></i>' . ' Begin of the list',
                        'url' => [
                            '/page/page/create',
                            'id' => $currentModelId,
                            'location' => TreeView::INSERT_BEGIN_LIST
                        ]
                    ],
                    [
                        'label' => '<i class="fa fa-angle-up"></i>' . ' Before active',
                        'url' => [
                            '/page/page/create',
                            'id' => $currentModelId,
                            'location' => TreeView::INSERT_BEFORE_ITEM
                        ]
                    ],
                    [
                        'label' => '<i class="fa fa-angle-down"></i>' . ' After active',
                        'url' => [
                            '/page/page/create',
                            'id' => $currentModelId,
                            'location' => TreeView::INSERT_AFTER_ITEM
                        ]
                    ],
                    [
                        'label' => '<i class="fa fa-angle-double-down"></i>' . ' End of the list',
                        'url' => ['/page/page/create', 'id' => $currentModelId, 'location' => TreeView::INSERT_END_LIST]
                    ],
                ]
            ]); ?>
        </div>

        <?= Html::a('<i class="fa fa-arrow-up"></i>', [
            '/page/page/move',
            'id' => $model->id,
            'direction' => TreeView::MOVE_DIRECTION_UP
        ], [
            'class' => 'btn btn-default',
            'disabled' => !$model->movable_u || $model->isRoot(),
        ]); ?>

        <?= Html::a('<i class="fa fa-arrow-down"></i>', [
            '/page/page/move',
            'id' => $model->id,
            'direction' => TreeView::MOVE_DIRECTION_DOWN
        ], [
            'class' => 'btn btn-default',
            'disabled' => !$model->movable_d || $model->isRoot(),
        ]); ?>

        <?= Html::a('<i class="fa fa-arrow-left"></i>', [
            '/page/page/move',
            'id' => $model->id,
            'direction' => TreeView::MOVE_DIRECTION_LEFT
        ], [
            'class' => 'btn btn-default',
            'disabled' => !$model->movable_l || $model->isRoot(),
        ]); ?>

        <?= Html::a('<i class="fa fa-arrow-right"></i>', [
            '/page/page/move',
            'id' => $model->id,
            'direction' => TreeView::MOVE_DIRECTION_RIGHT
        ], [
            'class' => 'btn btn-default',
            'disabled' => !$model->movable_r || $model->isRoot(),
        ]); ?>

        <?= Html::a('<i class="fa fa-pencil"></i>', ['/page/page/update-page', 'id' => $model->id], [
            'class' => 'btn btn-default',
        ]); ?>

        <?= Html::a('<i class="fa fa-trash"></i>', ['/page/page/delete', 'id' => $model->id], [
            'class' => 'btn btn-default',
            'disabled' => !$model->removable,
            'data' => ['method' => 'POST', 'confirm' => bt('Are you sure you wnt to delete this item?')]
        ]); ?>
    <?php endif; ?>
</div>
