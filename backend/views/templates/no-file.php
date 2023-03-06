<?php
use yii\helpers\Html;
?>
<div class="container form-group">
    <div class="margined centered">
        <h3><?= Yii::t('back/fileMetaData', 'Upload file first.'); ?></h3>
    </div>
    <div class="col-sm-8 margined centered">
        <?= Html::a(Yii::t('back/fileMetaData', 'Ok'), '#', ['class' => 'btn btn-warning cancel-crop']); ?>
    </div>
</div>
