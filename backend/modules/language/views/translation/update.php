<?php

use yii\helpers\Html;
use common\components\model\ActiveRecord;
use backend\components\BackendModel;
use backend\modules\language\models\SourceMessage;

/**
 * @var $this yii\web\View
 * @var $model SourceMessage
 * @var $formConfig array
 * @var $langModel BackendModel|ActiveRecord|null
 */

$this->title = Yii::t('back/app', 'Update {modelClass}: ', ['modelClass' => $model->getTitle()]) . ' ' . $model->message;
$this->params['breadcrumbs'][] = ['label' => $model->getTitle(), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->message, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('back/app', 'Update');
$controller = $this->context;
?>

<div class="block block-themed">
    <div class="block-header bg-primary-dark">
        <h1 class="block-title pull-left"><i class="fa fa-pencil"></i> <?= Html::encode($this->title) ?></h1>
    </div>
    <div class="block-content">
        <?= $this->render('_form', [
            'model' => $model,
            'langModel' => null,
            'formConfig' => $formConfig
        ]) ?>
    </div>
</div>
