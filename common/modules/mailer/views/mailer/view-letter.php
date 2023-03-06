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

<div class="panel panel-default">
    <div class="panel-heading">
        <h1 class="panel-title pull-left"><?= Html::encode($this->title) ?></h1>
        <?= $model->isTranslatable() ? FormLangSwitcher::widget() : ''; ?>
        <div class="clearfix"></div>
    </div>

    <div class="panel-body">
        <p>
            <?= Html::a(Yii::t('back/mailer', 'Send again'), ['resend', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
        </p>

        <?= LanguageDetailView::widget([
            'model' => $model,
            'attributes' => $model->getColumns('view'),
        ]) ?>
    </div>

</div>
