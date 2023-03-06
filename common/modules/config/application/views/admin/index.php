<?php

use backend\widgets\FormLangSwitcher;
use yii\helpers\Html;
use common\modules\config\application\widgets\ConfigLangSwitcher;
use common\modules\config\infrastructure\services\IConfigEntityFormRenderer;

/**
 * @var $this yii\web\View
 * @var $form IConfigEntityFormRenderer
 */
$this->title = bt('Application configuration', 'config');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box box-primary border-black">
    <div class="box-header">
        <h1 class="box-title"><?= Html::encode($this->title) ?></h1>
        <?= FormLangSwitcher::widget(); ?>
    </div>

    <div class="box-body">
        <?= $form->renderForm() ?>
    </div>

</div>
