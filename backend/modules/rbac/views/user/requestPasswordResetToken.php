<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \backend\modules\rbac\models\form\PasswordResetRequest */

$this->title = 'Request password reset';
?>
<div class="site-request-password-reset">

    <p class="login-box-msg">Please fill out your email. A link to reset password will be sent there.</p>


            <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>
                <?= $form->field($model, 'email') ?>
                <div class="form-group">
                    <?= Html::submitButton(Yii::t('rbac-admin', 'Send'), ['class' => 'btn btn-primary']) ?>
                </div>
            <?php ActiveForm::end(); ?>
</div>
<a href="<?=\yii\helpers\Url::to(['/'])?>">Back</a><br>