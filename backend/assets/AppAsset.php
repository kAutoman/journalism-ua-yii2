<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\assets;

use dmstr\web\AdminLteAsset;
use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.css',
//        '//code.ionicframework.com/ionicons/1.5.2/css/ionicons.min.css',
        //
        'css/theme/custom_lte.css',
        'css/theme/select2_v4.0.3.css',
        'css/theme/slick.css',
        //
        'css/flag/flag-icon.css',
        //
        'css/backend.css'
    ];
    public $js = [
        'https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js',
        'https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js',
//        'js/jquery.li-translit.js',
//        'js/jquery.mCustomScrollbar.min_v2.8.1.js',
        'js/jquery.slimscroll.min.js',
        'js/vintage-popup.js',
        'js/slick.js',
        'js/backend.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\jui\JuiAsset',
        AdminLteAsset::class
    ];
}
