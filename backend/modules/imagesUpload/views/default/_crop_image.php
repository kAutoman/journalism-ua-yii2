<?php
use \backend\modules\imagesUpload\models\ImagesUploadModel;
use metalguardian\fileProcessor\helpers\FPM;
use yii\helpers\Html;

\backend\assets\CropperAsset::register($this);
?>
<div class="container">
    <div class="img-container col-sm-6">
        <img src="<?= FPM::originalSrc($id).'?'.time(); ?>"/>
    </div>

    <div class="img-preview col-sm-6"></div>

    <div class="col-sm-12">
        <?= Html::hiddenInput('dataX', null, ['id' => 'dataX']); ?>
        <?= Html::hiddenInput('dataY', null, ['id' => 'dataY']); ?>
        <?= Html::hiddenInput('dataHeight', null, ['id' => 'dataHeight']); ?>
        <?= Html::hiddenInput('dataWidth', null, ['id' => 'dataWidth']); ?>
        <?= Html::hiddenInput('fileId', $id, ['id' => 'fileId']); ?>
    </div>

    <div class="col-sm-8 margined centered">
        <?= Html::a('Сохранить', ImagesUploadModel::getSaveCroppedImageUrl(), ['class' => 'btn btn-info save-cropped']); ?>
        <?= Html::a('Отмена', '#', ['class' => 'btn btn-warning cancel-crop']); ?>
    </div>
</div>


