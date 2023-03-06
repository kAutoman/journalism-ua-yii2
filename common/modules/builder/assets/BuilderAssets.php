<?php

namespace common\modules\builder\assets;

use Yii;
use yii\web\AssetBundle;

/**
 * Class BuilderAssets
 *
 * @package common\modules\builder\assets
 */
class BuilderAssets extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = __DIR__;

    public $js = [
        '//cdn.jsdelivr.net/gh/highlightjs/cdn-release@9.16.2/build/highlight.min.js',
        'js/builder.js'
    ];
    public $css = [
        '//cdn.jsdelivr.net/gh/highlightjs/cdn-release@9.16.2/build/styles/default.min.css',
        'css/builder.css'
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        'backend\assets\AppAsset'
    ];
}
