<?php

namespace common\modules\mailer\assets;

use yii\web\AssetBundle;

/**
 * Class MailerAssets
 *
 * @package common\modules\mailer\assets
 */
class MailerAssets extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = __DIR__;

    public $js = [
        'js/mailer.js'
    ];
    public $css = [];

    /**
     * @inheritdoc
     */
    public $depends = [
        'backend\assets\AppAsset'
    ];
}
