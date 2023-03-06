<?php

use backend\widgets\FormLangSwitcher;
use yii\helpers\Html;
use common\modules\config\infrastructure\services\IConfigEntityFormRenderer;
use yii\helpers\Inflector;

/**
 * @var $this yii\web\View
 * @var $form IConfigEntityFormRenderer
 */
$this->title = ucfirst(Inflector::camel2words($this->title, false));
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box box-primary border-black  config-module">
    <div class="box-header">
        <h1 class="box-title"><?= Html::encode($this->title) ?></h1>
        <?= FormLangSwitcher::widget(); ?>
    </div>
    <div class="box-body">
        <?= $form->renderForm() ?>
    </div>
</div>
