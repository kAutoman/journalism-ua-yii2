<?php

namespace common\modules\config\application;

use yii\web\AssetBundle;

class ConfigAsset extends AssetBundle
{
    public $sourcePath = '@common/modules/config/application/assets';

    public $css = [
        'css/config.css',
    ];

    public $js = [
        'js/config.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];
}
