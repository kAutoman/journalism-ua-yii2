<?php
/**
 * @var $relatedModels ActiveRecord[]
 * @var int $min
 * @var int $max
 */
//var_dump($min, $max);die;
use common\components\model\ActiveRecord;
use common\modules\dynamicForm\widgets\DynamicForm;

//var_dump($relatedModels);die;
$formName = $relatedModels[0]->formName();
?>
<div class="form-group">
    <?php DynamicForm::begin([
        'widgetContainer' => "df_wrapper_{$formName}",
        'widgetBody' => '.df-widget-body',
        'widgetItem' => '.df-widget-item',
        'limit' => $max ?? 999,
        'min' => $min ?? 0,
        'insertButton' => '.df-add', // css class
        'deleteButton' => '.df-remove', // css class
        'model' => $relatedModels[0],
        'formId' => 'main-form',
        'formFields' => ['dummy'],
    ]); ?>
    <div class="df-container">
        <div class="df-widget-body df-sortable">
            <?= $this->render('_form_fields', ['relatedModels' => $relatedModels]) ?>
        </div>
        <div class="button-add">
            <button type="button" data-className="<?= get_class($relatedModels[0]) ?>" class="btn btn-success df-add">
                <i class="fa fa-plus"></i> <?= Yii::t('back/app', 'Add'); ?>
            </button>
        </div>
    </div>
    <?php DynamicForm::end(); ?>
</div>
