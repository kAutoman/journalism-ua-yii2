<?php

use backend\components\FormBuilder;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * @var \backend\modules\imagesUpload\models\FileMetaData $model
 * @var string                                            $action
 * @var array                                             $translationModels
 */

?>
<?= Html::errorSummary(
    [$model],
    ['class' => 'alert alert-danger']
); ?>

<?php /** @var FormBuilder $form */
$form = FormBuilder::begin([
    'action'                 => $action,
    'enableClientValidation' => false,
    'options'                => [
        'id'      => 'meta-data-form',
        'enctype' => 'multipart/form-data',
        'data'    => ['pjax' => true],
    ],

]); ?>
<div class="col-sm-7">
    <?= $form->prepareRows($model, $model->getFormConfig()); ?>
</div>

<div class="col-sm-7">
    <?= Html::submitButton(Yii::t('back/fileMetaData', 'Save'), ['class' => 'btn btn-success']); ?>
    <?= Html::a(Yii::t('back/fileMetaData', 'Cancel'), '#', ['class' => 'btn btn-warning cancel-crop', 'data-dismiss' => 'modal']); ?>
</div>

<?php FormBuilder::end(); ?>
