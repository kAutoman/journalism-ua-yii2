<?php
namespace backend\components\gii\assets;


use yii\bootstrap\BootstrapPluginAsset;
use yii\gii\GiiAsset as BaseGiiAsset;
use yii\web\AssetBundle;

/**
 * Created by anatolii
 */
class GiiAsset extends AssetBundle
{
    public $sourcePath = '@app/components/gii/assets';
    public $css = [
        'main.css',
    ];
    public $js = [
        'gii.js',
        'typeahead.bundle.js'
    ];
    public $depends = [
        BaseGiiAsset::class,
        JuiAsset::class,
        BootstrapPluginAsset::class
    ];
}
