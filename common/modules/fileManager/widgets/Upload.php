<?php

namespace common\modules\fileManager\widgets;

use backend\components\gii\assets\JuiAsset;
use trntv\filekit\widget\Upload as BaseUpload;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;

/**
 * Class Upload
 *
 * @package common\modules\fileManager\widgets
 */
class Upload extends BaseUpload
{
    /** @var bool */
    public $sortable = true;
    /** @var bool */
    public $multiple = true;
    /** @var float|int Size in bytes. */
    public $maxFileSize = 5 * 1024 * 1024; // 5Mb
    /** @var bool */
    public $showPreviewFilename = false;
    /** @var array */
    public $url = ['/file-manager/file/upload'];
    /** @var array */
    public $acceptFileTypes = ['jpeg', 'jpg', 'png'];
    /** @var int */
    public $maxNumberOfFiles = 1;

    /**
     * @var null|array
     * @todo
     */
    public $extraFields = ['alt'];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->acceptFileTypes = $this->arrayToRegExp();


        $this->clientOptions = ArrayHelper::merge([
            'extraFields' => $this->extraFields,
            'modelClass' => get_class($this->model),
            'modelAttribute' => $this->attribute,
            'altLabel' => bt('Alt label', 'file_manager'),
            'titleLabel' => bt('Title label', 'file_manager')
        ], $this->clientOptions);

        return parent::init();
    }

    /**
     * @return string
     */
    public function run()
    {
        $this->registerClientScript();
        $content = Html::beginTag('div');
        $content .= Html::hiddenInput($this->name, null, [
            'class' => 'empty-value',
            'id' => $this->hiddenInputId === null ? $this->options['id'] : $this->hiddenInputId
        ]);
        $content .= Html::fileInput($this->getFileInputName(), null, [
            'name' => $this->getFileInputName(),
            'id' => $this->getId(),
            'multiple' => $this->multiple,
        ]);
        $content .= Html::endTag('div');

        return $content;
    }

    /**
     * Convert array of allowed types to string and place them to reg. exp.
     *
     * @return JsExpression|null
     */
    private function arrayToRegExp(): ?JsExpression
    {
        if (!empty($this->acceptFileTypes)) {
            $types = implode("|", $this->acceptFileTypes);
            return new JsExpression("/(\.|\/)({$types})$/i");
        }
        return null;
    }

    /**
     * Registers required script for the plugin to work as jQuery File Uploader
     */
    public function registerClientScript()
    {
        UploadAssets::register($this->getView());
        $options = Json::encode($this->clientOptions);
        if ($this->sortable) {
            JuiAsset::register($this->getView());
        }
        $this->getView()->registerJs("jQuery('#{$this->getId()}').yiiUploadKit({$options});");
    }
}
