<?php
use \backend\modules\imagesUpload\models\ImagesUploadModel;

?>
<a class="crop-link btn btn-xs btn-default pull-right" data-toggle="modal" href="<?= ImagesUploadModel::getCropUrl(['id' => '']) ?>" {dataKey} data-target=".bs-cropper-modal" data-backdrop="static">
    <i class="glyphicon glyphicon glyphicon-scissors file-icon-large text-success"></i>
</a>