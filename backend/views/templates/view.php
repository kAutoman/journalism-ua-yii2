<?php

use yii\helpers\Html;
use backend\widgets\FormLangSwitcher;
use backend\components\LanguageDetailView;

/**
 * @var $this yii\web\View
 * @var $model backend\components\BackendModel|common\components\model\ActiveRecord
 * @var $controller backend\components\BackendController
 */

$controller = $this->context;
$this->title = $model->hasAttribute('label') && $model->label ? $model->label : $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', $model->getTitle()), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box box-primary border-black">
    <div class="box-header">
        <h1 class="box-title pull-left"><?= Html::encode($this->title) ?></h1>
        <?= $model->isTranslatable() ? FormLangSwitcher::widget() : ''; ?>
        <div class="clearfix"></div>
    </div>

    <div class="box-body">
        <p>
            <?= Html::a('<i class="fa fa-arrow-left"></i> ' . bt('Go back'), ['index'], ['class' => 'btn btn-warning']) ?>
            <?php if ($controller->canUpdate) : ?>
                <?= Html::a(Yii::t('back/app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?php endif; ?>
            <?php if ($controller->canDelete) : ?>
                <?= Html::a(Yii::t('back/app', 'Delete'), ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ]) ?>
            <?php endif; ?>
            <?php if ($controller->canCreate) : ?>
                <?= Html::a(Yii::t('back/app', 'Create'), ['create'], ['class' => 'btn btn-success']) ?>
            <?php endif; ?>
        </p>

        <?= LanguageDetailView::widget([
            'model' => $model,
            'attributes' => $model->getColumns('view'),
        ]) ?>
    </div>

</div>
