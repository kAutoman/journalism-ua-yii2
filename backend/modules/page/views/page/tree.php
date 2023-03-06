<?php

use backend\modules\page\models\PagesTree;
use yii\bootstrap\Html;
use yii\web\JsExpression;
use backend\modules\page\models\Page;
use backend\widgets\FormLangSwitcher;
use paulzi\nestedsets\NestedSetsBehavior;
use backend\modules\page\widgets\TreeView;
use backend\modules\page\assets\TreeViewAssets;

/**
 * @var Page|NestedSetsBehavior $model
 */

TreeViewAssets::register($this);

$editPage = false;

if ($model->getIsNewRecord() || $model->updatePage) {
    $editPage = true;
}
?>

<div class="row">
    <div class="col-md-3">
        <?= TreeView::widget([
            'id' => 'tree',
            'model' => $model,
            'data' => PagesTree::getPagesTree(),
            'template' => TreeView::TEMPLATE_BOXED,
            'size' => TreeView::SIZE_NORMAL,
            'header' => 'Pages',
            'searchOptions' => [
                'inputOptions' => [
                    'placeholder' => 'Search...'
                ],
                'clearButtonOptions' => [
                    'title' => 'Clear',
                ],
            ],
            'clientOptions' => [
                'onNodeSelected' => new JsExpression("
                    function (event, item) {
                        window.location.href = item.href;
                    }"
                ),
                'selectedBackColor' => '#3c8dbc',
                'borderColor' => '#d2d6de',
                'levels' => 5,
                'enableLinks' => true,
            ],
        ]); ?>

    </div>
    <div class="col-md-9">
        <div class="box box-primary border-black">
            <div class="box-header">
                <h1 class="box-title"><?= $model->getIsNewRecord() ? 'New page' : $model->label ?></h1>
                <?= $model->isTranslatable() ? FormLangSwitcher::widget() : ''; ?>
            </div>

            <div class="box-body">
                <?php if ($model->updatePage): ?>
                    <div class="alert alert-warning">
                        <?= Html::icon("alert") . Yii::t('form',
                            "&nbsp;&nbsp;&nbsp;<b>Attention</b>, when saving, all old data will be deleted") ?>
                    </div>
                <?php endif; ?>

                <?= $this->render('//templates/_form', [
                    'model' => $model,
                    'formConfig' => ($editPage) ? 'getCreateForm' : 'getFormConfig',
                    'enableAjaxValidation' => false
                ]) ?>
            </div>

        </div>

    </div>
</div>



