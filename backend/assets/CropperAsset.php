<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Class CropperAsset
 * @package backend\assets
 */
class CropperAsset extends AssetBundle
{
    public $sourcePath = '@vendor/xutl/yii2-cropper-asset/assets';

    public $css = [
        'cropper.min.css',
    ];
    public $js = [
        'cropper.min.js',
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
