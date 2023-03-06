<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('rbac-admin', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>
    <h1>Update user <?= $model->username ?></h1>
    <?= $form->field($model, 'username')->textInput() ?>
    <?= $form->field($model, 'email')->textInput() ?>

    <?= $form->field($model, 'memberItemList')->dropDownList(\common\models\MemberItem::getListItems(), [
        'multiple' => true
    ]) ?>

    <?= $form->field($model, 'status')->dropDownList(\backend\modules\rbac\models\User::getListStatuses()) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('rbac-admin', 'Create') : Yii::t('rbac-admin', 'Update'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
