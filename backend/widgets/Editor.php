<?php

namespace backend\widgets;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;
use dosamigos\tinymce\TinyMceAsset;
use dosamigos\tinymce\TinyMceLangAsset;

/**
 * Class Editor
 *
 * @package backend\widgets
 */
class Editor extends InputWidget
{
    /**
     * @var string editor presets constants.
     */
    const TYPE_NONE = 'none';
    const TYPE_FULL = 'full';
    const TYPE_MIN = 'minimal';

    /**
     * @var string the language to use. Defaults to null (en).
     */
    public $language;

    /**
     * @var array the options for the TinyMCE JS plugin.
     * Please refer to the TinyMCE JS plugin Web page for possible options.
     * @see http://www.tinymce.com/wiki.php/Configuration
     */
    public $clientOptions = [];

    /**
     * @var bool whether to set the on change event for the editor. This is required to be able to validate data.
     * @see https://github.com/2amigos/yii2-tinymce-widget/issues/7
     */
    public $triggerSaveOnBeforeValidateForm = true;

    /**
     * @var string editor options preset.
     */
    public $preset = self::TYPE_MIN;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->hasModel()) {
            echo Html::activeTextarea($this->model, $this->attribute, $this->options);
        } else {
            echo Html::textarea($this->name, $this->value, $this->options);
        }
        $this->registerClientScript();
    }

    /**
     * Registers tinyMCE js plugin
     */
    protected function registerClientScript()
    {
        $js = [];
        $view = $this->getView();
        TinyMceAsset::register($view);
        $id = $this->options['id'];
        $this->clientOptions['selector'] = "#$id";
        if ($this->language === null) {
            $this->language = Yii::$app->language;
        }
        if ($this->language !== 'en') {
            $langFile = "langs/{$this->language}.js";
            $langAssetBundle = TinyMceLangAsset::register($view);
            $langAssetBundle->js[] = $langFile;
            $this->clientOptions['language'] = $this->language;
            $this->clientOptions['language_url'] = $langAssetBundle->baseUrl . "/{$langFile}";
        }
        $this->applyPreset();
        $options = Json::encode($this->clientOptions);

        $js[] = "tinymce.init($options);";
        $js[] = "$('.mce-tinymce *[title]').tooltip('disable');";
        if ($this->triggerSaveOnBeforeValidateForm) {
            $js[] = "$('#{$id}').parents('form').on('beforeValidate', function() {tinymce.triggerSave();});";
        }
        $view->registerJs(implode("\n", $js));
    }

    /**
     * Apply editor preset configuration.
     */
    public function applyPreset()
    {
        switch ($this->preset) {
            case self::TYPE_NONE;
                break;
            case self::TYPE_FULL;
                $this->addFullOptions();
                break;
            case self::TYPE_MIN;
                $this->addMinimalOptions();
                break;
        }
    }

    /**
     * Add full options.
     */
    public function addFullOptions()
    {
        $opt = [
            'height' => 500,
            'theme' => 'modern',
            'code_dialog_width' => 800,
            'image_advtab' => true,
            'relative_urls' => false,
            'branding' => false,
            /** @see https://tech.yandex.ru/speller/doc/dg/tasks/how-to-spellcheck-tinymce-docpage */
            'spellchecker_language' => Yii::$app->language,
            'spellchecker_languages' => 'Russian=ru,Ukrainian=uk,English=en',
            'spellchecker_rpc_url' => '//speller.yandex.net/services/tinyspell',
            'plugins' => '
                print preview fullscreen paste searchreplace autolink autoresize textpattern
                code visualblocks visualchars image link media template 
                table charmap hr nonbreaking anchor insertdatetime contextmenu
                advlist lists wordcount spellchecker 
            ', // colorpicker textcolor codesample pagebreak toc imagetools
            'menu' => [
                'file' => ['title' => 'File', 'items' => 'newdocument print'],
                'edit' => ['title' => 'Edit', 'items' => 'selectall | searchreplace'],
                'insert' => ['title' => 'Insert', 'items' => 'anchor charmap insertdatetime nonbreaking'],
                'view' => ['title' => 'View', 'items' => 'visualblocks preview fullscreen'],
                'format' => [
                    'title' => 'Format',
                    'items' => 'bold italic underline strikethrough superscript subscript | formats | removeformat'
                ],
                'table' => ['title' => 'Table', 'items' => 'inserttable tableprops deletetable | cell row column'],
                'tools' => ['title' => 'Tools', 'items' => 'spellchecker code'],
            ],
            /** @see https://www.tinymce.com/docs/advanced/editor-control-identifiers/#toolbarcontrols */
            'toolbar1' => '
                code | undo redo | bold italic underline strikethrough | superscript subscript |
                alignleft aligncenter alignright alignjustify |
                numlist bullist outdent indent | removeformat | spellchecker |
                formatselect | cut copy paste pastetext | media link image | hr visualchars | blockquote |
            ',// forecolor backcolor
            'image_dimensions' => false,
            'images_upload_url' => Url::to(['/tiny-mce/upload']),
        ];

        $this->clientOptions = ArrayHelper::merge($opt, $this->clientOptions);
    }

    /**
     * Add minimal options.
     */
    public function addMinimalOptions()
    {
        $opt = [
            'height' => 400,
            'theme' => 'modern',
            'code_dialog_width' => 800,
            'image_advtab' => true,
            'branding' => false,
            'relative_urls' => false,
            /** @see https://tech.yandex.ru/speller/doc/dg/tasks/how-to-spellcheck-tinymce-docpage */
            'spellchecker_language' => Yii::$app->language,
            'spellchecker_languages' => 'Russian=ru,Ukrainian=uk,English=en',
            'spellchecker_rpc_url' => '//speller.yandex.net/services/tinyspell',
            'block_formats' => 'Paragraph=p;Header 2=h2;Header 3=h3;Header 4=h4;Header 5=h5;Header 6=h6',
            'menubar' => false,
            /** @see https://www.tinymce.com/docs/advanced/editor-control-identifiers/#toolbarcontrols */
             //Default template
            'plugins' => '
                paste searchreplace autolink autoresize textpattern
                code visualblocks visualchars link hr anchor contextmenu
                lists wordcount spellchecker
            ',
             'toolbar1' => '
                code | formatselect removeformat | undo redo | bold italic underline strikethrough | superscript subscript |
                alignleft aligncenter alignright alignjustify |
                numlist bullist | hr blockquote link | pastetext |
            ',
        ];

        $this->clientOptions = ArrayHelper::merge($opt, $this->clientOptions);
    }
}
