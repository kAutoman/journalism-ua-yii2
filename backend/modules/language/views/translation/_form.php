<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use common\helpers\LanguageHelper;
use common\components\model\ActiveRecord;
use backend\components\FormBuilder;
use backend\components\BackendModel;

/**
 * @var $this yii\web\View
 * @var $model ActiveRecord|BackendModel
 * @var $langModel ActiveRecord|null
 * @var $languageModel BackendModel
 * @var $form FormBuilder
 * @var $formConfig array
 */

$action = isset($action) ? $action : '';
$currentEditLang = LanguageHelper::getEditLanguage();
?>

<div class="main-form">
    <?php $form = FormBuilder::begin([
        'action' => $action,
        'enableClientValidation' => true,
        'options' => [
            'id' => 'main-form',
            'class' => 'block',
        ]
    ]); ?>

    <?php
    $items = [];
    foreach (call_user_func([$model, $formConfig]) as $tabName => $tabConfig) {
        $items[] = [
            'label' => $tabName,
            'content' => $form->prepareRows($model, $tabConfig, $langModel),
        ];
    }

    echo Tabs::widget(['items' => $items, 'navType' => 'nav-tabs nav-tabs-alt']);
    ?>

    <div class="row">
        <div class="col-sm-12">
            <div class="btn-group">
                <?= Html::submitButton(Yii::t('back/app', 'Save'), ['class' => 'btn btn-success']); ?>
            </div>
            <?= Html::a(Yii::t('back/app', 'Cancel'), ['index'], ['class' => 'btn btn-danger']); ?>
        </div>
    </div>

    <?php FormBuilder::end(); ?>

</div>
