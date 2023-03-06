<?php

use common\modules\seo\behaviors\MetaTagsBehavior;
use common\modules\seo\widgets\MetaTagsForm;
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
 * @var $enableAjaxValidation bool
 */

$action = isset($action) ? $action : '';
$currentEditLang = LanguageHelper::getEditLanguage();
$validationUrl = ['ajax-validation'];
if (!$model->getIsNewRecord()) {
    $validationUrl['id'] = $model->id;
}
?>

<div class="main-form">
    <?php $form = FormBuilder::begin([
        'action' => $action,
        'enableClientValidation' => true,
        'enableAjaxValidation' => $enableAjaxValidation ?? false,
        'validationUrl' => $validationUrl,
        'options' => ['id' => 'main-form', 'class' => 'block']
    ]) ?>

    <?php $items = [];
    foreach (call_user_func([$model, $formConfig]) as $tabName => $tabConfig) {
        $items[] = ['label' => $tabName, 'content' => $form->prepareRows($model, $tabConfig)];
    } ?>

    <?php
    $seo = $model->getBehavior('seo');
    if ($seo && $seo instanceof MetaTagsBehavior) {
        $items[] = ['label' => 'SEO', 'content' => MetaTagsForm::widget(['form' => $form, 'model' => $model])];
    }
    ?>

    <div class="nav-tabs-custom tab-primary">
        <?= Tabs::widget(['items' => $items, 'navType' => 'nav-tabs nav-tabs-alt']) ?>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="btn-group">
                <?= Html::submitButton(Yii::t('back/app', 'Save'), ['class' => 'btn btn-flat btn-success black']); ?>
            </div>
        </div>
    </div>

    <?php FormBuilder::end() ?>
</div>
