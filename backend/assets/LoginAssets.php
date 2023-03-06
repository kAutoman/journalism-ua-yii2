<?php

namespace backend\assets;

use dmstr\web\AdminLteAsset;
use yii\web\AssetBundle;

/**
 * Class LoginAssets
 *
 * @package backend\assets
 */
class LoginAssets extends AssetBundle
{
    public $js = [
        'js/login/TweenLite.min.js',
        'js/login/EasePack.min.js',
        'js/login/login-bg.js'
    ];
    public $css = [
        'css/login/login.css'
    ];
    public $depends = [
        AdminLteAsset::class
    ];
}
