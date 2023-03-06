<?php

use yii\helpers\Html;
use common\components\model\ActiveRecord;
use backend\components\BackendModel;
use backend\widgets\FormLangSwitcher;

/**
 * @var $this yii\web\View
 * @var $model BackendModel|ActiveRecord
 * @var $formConfig array
 * @var $enableAjaxValidation bool
 */

$this->title = Yii::t('back/app', 'Update {modelClass}: ', ['modelClass' => $model->getTitle()]) . ' ' . ($model->hasAttribute('label') && $model->label ? $model->label : $model->id);
$this->params['breadcrumbs'][] = ['label' => $model->getTitle(), 'url' => ['index']];
$this->params['breadcrumbs'][] = [
    'label' => $model->hasAttribute('label') && $model->label ? $model->label : $model->id,
    'url' => ['view', 'id' => $model->id]
];
$this->params['breadcrumbs'][] = Yii::t('back/app', 'Update');
$controller = $this->context;
?>
<div class="box box-primary border-black">
    <div class="box-header">
        <h1 class="box-title"><?= Html::encode($this->title) ?></h1>
        <?= $model->isTranslatable() ? FormLangSwitcher::widget() : ''; ?>
    </div>

    <div class="box-body">
        <?= $this->render('_form', [
            'model' => $model,
            'formConfig' => $formConfig,
            'enableAjaxValidation' => $enableAjaxValidation
        ]) ?>
    </div>

</div>
