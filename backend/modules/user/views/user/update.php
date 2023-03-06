<?php

use metalguardian\fileProcessor\helpers\FPM;
use yii\helpers\Html;
use backend\modules\user\models\User;

/**
 * @var $this yii\web\View
 * @var $model User
 * @var $formConfig array
 */

$this->title = Yii::t('back/app', 'Update {modelClass}: ', [
        'modelClass' => $model->getTitle(),
    ]) . ' ' . $model->username;

$this->params['breadcrumbs'][] = ['label' => $model->getTitle(), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->username, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('back/app', 'Update');

?>

<div class="row">
    <div class="col-sm-2">
        <?= Html::img($model->avatarSrc ? FPM::originalSrc($model->avatarSrc->file_id) : '/img/no_image.svg', ['width' => '100%']); ?>
        <?= Html::a('Change password', ['change-password', 'id' => $model->id], ['class' => 'btn btn-primary']); ?>
    </div>
    <div class="col-sm-10">
        <div class="block block-themed">
            <div class="block-header bg-primary-dark">
                <h1 class="block-title pull-left"><i class="fa fa-pencil"></i> <?= Html::encode($this->title) ?></h1>
            </div>
            <div class="block-content">
                <?= $this->render('//templates/_form', [
                    'model' => $model,
                    'formConfig' => $formConfig
                ]) ?>
            </div>
        </div>
    </div>
</div>

