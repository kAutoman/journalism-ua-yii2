<?php

use metalguardian\fileProcessor\helpers\FPM;

/**
 * @var $model \common\modules\builder\models\SampleModel
 */
?>

<div class="row">
    <div class="col-sm-12">
        <h3 class="text-center"><?= $model->label; ?></h3>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <p><?= $model->description; ?></p>
    </div>

    <div class="col-sm-6">
        <a href="<?= FPM::originalSrc($model->image); ?>" target="_blank">
            description link (image attribute)
        </a>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <p><?= $model->content; ?></p>
    </div>
</div>
<div class="row">
    <?php foreach (json_decode($model->images) as $key => $image) : ?>
        <a href="<?= FPM::originalSrc($image); ?>" target="_blank">
            file (image) <?= $key + 1; ?>
        </a><br>
    <?php endforeach; ?>
</div>
<hr>
