<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = Yii::t('back/login', 'Login');

?>

<div class="login-page">

    <div id="login-header" class="login-header">
        <canvas id="login-canvas"></canvas>
        <div class="main-title">
            <h1><span class="thin">Vintage</span> CMS</h1>
            <div class="login-box box-warning">
                <div class="login-box-body">
                    <p class="login-box-msg">Sign in to start your session</p>
                    <?php $form = ActiveForm::begin([
                        'id' => 'login-form',
                        'method' => 'POST',
                        //        'options' => ['class' => 'form-horizontal push-30-t']
                    ]); ?>

                    <?= $form->field($model, 'email')
                        ->textInput(['placeholder' => $model->getAttributeLabel('email')])
                        ->label(false); ?>

                    <?= $form->field($model, 'password')
                        ->passwordInput(['placeholder' => $model->getAttributeLabel('password')])
                        ->label(false); ?>

                <div class="row">
                    <div class="col-xs-8">
                <?php if (Yii::$app->user->enableAutoLogin) : ?>
                    <?= $form->field($model, 'rememberMe')->checkbox() ?>
                <?php endif; ?>
                    </div>
                    <div class="col-xs-4">
                        <?= Html::submitButton(
                            '<i class="fa fa-arrow-right push-5-r"></i> '
                            . Yii::t('back/login','Login'),
                            [
                                'class' => 'btn btn-sm btn-primary login-btn',
                                'name' => 'login-button'
                            ])
                        ?>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>

</div>

<style>

</style>

<!--<div class="login-box">-->
<!--    <div class="login-logo">-->
<!--        <a href="/"><b></b></a>-->
<!--    </div>-->
<!---->
<!--    <div class="login-box-body">-->
<!--        <p class="login-box-msg">Sign in to start your session</p>-->
<!--        -->
<!--    </div>-->
<!--</div>-->

