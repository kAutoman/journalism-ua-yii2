<?php

namespace common\modules\fileManager\widgets;

use trntv\filekit\widget\BlueimpFileuploadAsset;
use trntv\filekit\widget\UploadAsset;
use yii\bootstrap\BootstrapAsset;
use yii\web\JqueryAsset;

/**
 * Class UploadAssets
 *
 * @package common\modules\fileManager\widgets
 */
class UploadAssets extends \yii\web\AssetBundle
{
    public $depends = [
        JqueryAsset::class,
        BootstrapAsset::class,
        BlueimpFileuploadAsset::class
    ];

    public $sourcePath = __DIR__ . '/assets';

    public $css = [
        'css/upload-kit.css'
    ];

    public $js = [
        'js/upload-kit.js'
    ];
}
