<?php
/**
 * @var \common\components\model\ActiveRecord $model
 */
use metalguardian\fileProcessor\helpers\FPM;
use yii\helpers\Html;

\backend\assets\CropperAsset::register($this);
?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel"><?= Yii::t('app', 'Crop image') ?></h4>
        </div>
        <?= Html::beginForm(['save-preview', 'id' => $model->id], 'post', ['class' => 'ajax-form', 'id' => 'crop-preview']); ?>
        <div class="modal-body">
            <div class="cropped-image" style="max-height: 400px;">
                <img src="<?= FPM::originalSrc($model->image_id) . '?' . time(); ?>"/>
            </div>

            <div>
                <?= Html::hiddenInput('dataX', null, ['id' => 'dataX']); ?>
                <?= Html::hiddenInput('dataY', null, ['id' => 'dataY']); ?>
                <?= Html::hiddenInput('dataHeight', null, ['id' => 'dataHeight']); ?>
                <?= Html::hiddenInput('dataWidth', null, ['id' => 'dataWidth']); ?>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= Yii::t('app', 'Cancel') ?></button>
            <input type="submit" class="btn btn-primary" value="<?= Yii::t('app', 'Save preview image') ?>" />
        </div>
        <?= Html::endForm(); ?>
    </div>
</div>


