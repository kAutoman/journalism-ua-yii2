<?php
/**
 * @var $relatedModels ActiveRecord[]
 */
use backend\components\FormBuilder;
use common\components\model\ActiveRecord;
use common\modules\dynamicForm\widgets\DummyFormBuilder;
use yii\helpers\Html;

$isAjax = false;
if (!isset($form)) {
    $form = DummyFormBuilder::begin([
        'id' => 'dummy-dynamic-form'
    ]);
    $isAjax = true;
}
?>
<!--<div class="df-container df-sortable">-->
<?php foreach ($relatedModels as $index => $model) : ?>
    <?php // var_dump($model->getErrors(), $_POST); ?>
    <div class="form-group df-widget-item filled item-<?= $index ?>" data-index="<?= $index ?>">
        <div class="df-icons-group">
            <div class="df-icon text-primary df-sortable-handle"><i class="glyphicon glyphicon-move"></i></div>
            <div class="df-icon df-remove text-danger"><i class="glyphicon glyphicon-trash"></i></div>
        </div>
        <?php
        // necessary for update action.
        if (!$model->getIsNewRecord()) {
            echo Html::activeHiddenInput($model, "[{$index}]id");
        }
        $formConfig = $model->getFormConfig($index)['Main'];
        ?>

        <?php foreach ($formConfig as $attr => $el) : ?>
            <?= $form->renderField($model,  "[{$index}]$attr", $el);  ?>
            <?php if (isset($model->errors["[{$index}]$attr"][0])) : ?>
                <div class="help-block has-error"><?= $model->errors["[{$index}]$attr"][0] ?></div>
            <?php endif ?>
        <?php endforeach ?>
    </div>
<?php endforeach; ?>
<!--</div>-->
<?php
if ($isAjax) {
    DummyFormBuilder::end();
}
?>
