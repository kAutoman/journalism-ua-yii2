<?php

namespace backend\modules\page\assets;

use yii\web\AssetBundle;
use backend\assets\AppAsset;

/**
 * Class TreeViewAssets
 *
 * @package backend\modules\page\assets
 */
class TreeViewAssets extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@bower/patternfly-bootstrap-treeview/dist';

    /**
     * @var array
     */
    public $js = [
        'bootstrap-treeview.min.js',
    ];

    /**
     * @var array
     */
    public $css = [
        'bootstrap-treeview.min.css',
    ];

    /**
     * @var array
     */
    public $depends = [AppAsset::class];
}
