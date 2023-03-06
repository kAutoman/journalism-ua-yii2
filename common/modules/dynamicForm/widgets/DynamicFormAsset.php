<?php

namespace common\modules\dynamicForm\widgets;

use yii\bootstrap\BootstrapAsset;
use yii\jui\JuiAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;
use yii\widgets\ActiveFormAsset;

/**
 * Asset bundle for Dynamic form Widget
 */
class DynamicFormAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $depends = [JqueryAsset::class, ActiveFormAsset::class, JuiAsset::class, BootstrapAsset::class];

    /**
     * Set up CSS and JS asset arrays based on the base-file names
     * @param string $type whether 'css' or 'js'
     * @param array $files the list of 'css' or 'js' base file names
     */
    protected function setupAssets($type, $files = [])
    {
        $srcFiles = [];
        foreach ($files as $file) {
            $srcFiles[] = "{$file}.{$type}";
        }
        if (empty($this->$type)) {
            $this->$type = $srcFiles;
        }
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setSourcePath(__DIR__ . '/assets');
        $this->setupAssets('js', ['yii2-dynamic-form']);
        $this->setupAssets('css', ['yii2-dynamic-form']);
        parent::init();
    }

    /**
     * Sets the source path if empty
     * @param string $path the path to be set
     */
    protected function setSourcePath($path)
    {
        if (empty($this->sourcePath)) {
            $this->sourcePath = $path;
        }
    }
}
