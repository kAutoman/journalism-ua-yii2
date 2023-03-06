<?php

use backend\widgets\FormLangSwitcher;
use yii\helpers\Html;
use backend\components\BackendModel;
use common\components\model\ActiveRecord;

/**
 * @var $this yii\web\View
 * @var $model BackendModel|ActiveRecord
 * @var $langModel BackendModel|ActiveRecord|null
 * @var $enableAjaxValidation bool
 */

$this->title = Yii::t('back/app', "Create {modelClass}", [
    'modelClass' => $model->getTitle(),
]);
$this->params['breadcrumbs'][] = ['label' => $model->getTitle(), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-primary border-black">
    <div class="box-header">
        <h1 class="box-title"><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="box-body">
        <?= $this->render('_form', [
            'model' => $model,
            'formConfig' => $formConfig,
            'enableAjaxValidation' => $enableAjaxValidation
        ]) ?>
    </div>
</div>
