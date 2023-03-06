<?php

use yii\widgets\Pjax;

/**
 * @var \backend\modules\imagesUpload\models\FileMetaData $model
 * @var string                                            $action
 * @var array                                             $translationModels
 */
?>
<div class="container form-group">
    <?php Pjax::begin(['timeout' => 5000, 'enablePushState' => false, 'id' => 'meta-data-form-container']); ?>
    <?= $this->render('parts/_form', [
        'action'            => $action,
        'model'             => $model,
    ]); ?>
    <?php Pjax::end(); ?>
</div>
