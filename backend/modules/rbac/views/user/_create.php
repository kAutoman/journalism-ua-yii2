<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('rbac-admin', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>
    <h1>Create user</h1>
    <?= $form->field($model, 'username')->textInput() ?>
    <?= $form->field($model, 'email')->textInput() ?>
    <?= $form->field($model, 'password')->textInput() ?>
    <?= $form->field($model, 'status')->dropDownList(\backend\modules\rbac\models\User::getListStatuses()) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rbac-admin', 'Create'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
