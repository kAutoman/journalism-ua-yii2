<?php

namespace common\modules\config\domain\values;

use backend\modules\imagesUpload\models\ImagesUploadModel;
use yii\helpers\Url;
use yii\bootstrap\Html;
use metalguardian\fileProcessor\helpers\FPM;
use metalguardian\fileProcessor\models\File;
use kartik\file\FileInput as FileInputWidget;
use common\helpers\LanguageHelper;

/**
 * Class FileInput
 *
 * @author Bogdan K. Fedun <delagics@gmail.com>
 */
class FileInput extends Field
{
    public function render(): string
    {
        $output = '';
        if (!$this->getIsDisplayable()) {
            return $output;
        }
        $id = $this->getInputId();
        $lang = LanguageHelper::getEditLanguage();
        $options = $this->preparedOptions();
        $multiple = obtain('multiple', $options, false);

        $webp = obtain('webp', $options, false);
        $url = $this->getIsAggregated()
            ? [
                '/config/aggregate/upload-file',
                'aggregate' => request()->get('aggregate'),
                'key' => $this->getKey(),
                'lang' => $lang,
                'multiple' => $multiple,
                'webp' => $webp,
                'limit'=>obtain('maxFileCount', $options, 1),
            ]
            : ['/config/admin/upload-file', 'key' => $this->getKey(), 'lang' => $lang, 'multiple' => $multiple, 'limit'=>obtain('maxFileCount', $options, 1)];
        $uploadUrl = Url::to($url);
        $allowedExtensions = obtain('allowedFileExtensions', $options);
        $extensionsAcceptMask = empty($allowedExtensions)
            ? '*'
            : call_user_func(function () use ($allowedExtensions) {
                $extensions = [];
                // clone array, do not use existing one
                $extensions = merge($extensions, $allowedExtensions);
                array_walk($extensions, function (&$item) {
                    $item = '.' . $item;
                });
                $result = implode(',', $extensions);
                return $result;
            });
        $previews = $this->getPreviews($this->getKey(), $lang, $multiple);
        $output .= $this->beforeRender();
        $output .= Html::label($this->getLabel(), $this->getInputId());
        $output .=  Html::a('', '/config/admin/sort-files', ['id' => 'urlForSorting']);
        //$output .= Html::hiddenInput($this->getName(), $this->getValue(), compact('id'));

        $output .= FileInputWidget::widget([
            'name' => $this->getName(),
            'options' => [
                'multiple' => $multiple,
                'accept' => $extensionsAcceptMask,
                'id' => $id
            ],
            'pluginEvents' => [
                "fileuploaded" => 'function(event, data, previewId, index) { 
                    var metaDataElem = $("#"+previewId).find(".file-actions .meta-btn");
                    if (metaDataElem.length !== 0) {
                        metaDataElem.attr("href", metaDataElem.attr("href") + data.response.id);
                    }
                }',
            ],
            'pluginOptions' => [
                'dropZoneEnabled' => true,
                'browseClass' => 'btn btn-success',
                'browseIcon' => '<i class="glyphicon glyphicon-picture"></i> ',
                'removeClass' => "btn btn-danger",
                'removeIcon' => '<i class="glyphicon glyphicon-trash"></i> ',
                'uploadClass' => "btn btn-info",
                'uploadIcon' => '<i class="glyphicon glyphicon-upload"></i> ',
                'uploadUrl' => $uploadUrl,
                'allowedFileExtensions' => obtain('allowedFileExtensions', $options),
                'allowedPreviewTypes' => ['image', 'video'], // for all other types - preview will be with simple icon
                'initialPreview' => obtain('initialPreview', $previews),
                'initialPreviewConfig' => obtain('initialPreviewConfig', $previews),
                'maxFileSize' => obtain('maxFileSize', $options, 0),
                'minFileCount' => obtain('minFileCount', $options, 1),
                'maxFileCount' => obtain('maxFileCount', $options, 1),
                'overwriteInitial' => false,
                'showRemove' => false,
                'otherActionButtons' => obtain('metaFields', $options)
                    ? $this->otherButtons()
                    : '',

                'previewSettings' => [
                    'video' => ['width' => '200px', 'height' => 'auto'],
                    'image' => ['width' => 'auto', 'height' => 'auto'],
                    'text' => ['width' => '200px', 'height' => '200px'],
                    'audio' => ['width' => '200px', 'height' => '200px'],
                    'flash' => ['width' => '200px', 'height' => '200px'],
                    'object' => ['width' => '200px', 'height' => '200px'],
                    'other' => ['width' => '200px', 'height' => '220px'],
                ],
                'pluginEvents' => [
                    'fileuploaded' => 'function(event, data, previewId, index) {
                       var elem = $("#"+previewId).find(".file-actions .file-footer-buttons .kv-file-remove");
                       elem.html("");
                       var img = $("#"+previewId).find("img");
                       img.attr("id", "preview-image-"+data.response.imgId);
                       elem.attr("data-url", data.response.deleteUrl);
                       elem.attr("data-key", data.response.id);
                       img.attr("src", data.response.url);
                       img.attr("data-id" , data.response.id);
                       elem.addClass("new-uploaded-image");
                       $(".file-upload-indicator .kv-file-remove").remove();
                       $("#"+previewId).attr("data-fileindex", "init_"+$(this).data("fileindex"));
                    }',
                    'filebatchuploadsuccess' => 'function(event, data) {
                        console.log(event, data);
                    ',
                ],
                'previewTemplates' => [
                    'video' => '<div class="file-preview-frame{frameClass}" id="{previewId}" data-fileindex="{fileindex}" title="{caption}">
                       <video width="{width}" height="{height}" controls><source src="{data}" type="{type}"></video>{footer}
                    </div>',
                    'image' => '<div class="file-preview-frame{frameClass}" id="{previewId}" data-fileindex="{fileindex}">
                        <img src="{data}" class="file-preview-image" title="{caption}" alt="{caption}">{footer}
                    </div>',
                    'text' => '<div class="file-preview-frame{frameClass}" id="{previewId}" data-fileindex="{fileindex}">
                        <div class="file-preview-text" title="{caption}">{data}</div>{footer}
                    </div>',
                    'html' => '<div class="file-preview-frame{frameClass}" id="{previewId}" data-fileindex="{fileindex}">
                        <object data="{data}" type="{type}" width="{width}" height="{height}"><div class="file-preview-other">{previewFileIcon}</div></object>{footer}
                    </div>',
                    'audio' => '<div class="file-preview-frame{frameClass}" id="{previewId}" data-fileindex="{fileindex}"title="{caption}">
                        <audio controls>
                            <source src="{data}" type="{type}"><div class="file-preview-other">{previewFileIcon}</div>
                        </audio>{footer}
                    </div>',
                    'other' => '<div class="file-preview-frame{frameClass}" id="{previewId}" data-fileindex="{fileindex}" title="{caption}">
                        <div class="file-preview-other">{previewFileIcon}</div>{footer}
                    </div>',
                    'generic' => '<div class="file-preview-frame{frameClass}" id="{previewId}" data-fileindex="{fileindex}">
                        {content}{footer}
                    </div>',
                ],
            ],
        ]);
        $output .= Html::tag('div', $this->getHint(), ['class' => 'hint-block']);
        $output .= $this->afterRender();

        return $output;
    }

    public function getDefaultOptions()
    {
        return [
            'id' => $this->getInputId(),
            'class' => 'form-control',
        ];
    }

    protected function getPreviews(string $key, string $lang, $isMultiple = false)
    {
        $initialPreview = [];
        $initialPreviewConfig = [];
        $filesId = $isMultiple ? explode(',', $this->getValue()) : $this->getValue();
        $files = \common\models\File::find()->andWhere(['id' => $filesId])->orderBy(['position'=>SORT_DESC])->all();
        if ($files) {
            /** @var File $file */
            foreach ($files as $file) {
                $fileName = $file->base_name . '.' . $file->extension;
                $initialPreview[] = $this->getPreviewContent($file);
                $url = $this->getIsAggregated()
                    ? [
                        '/config/aggregate/delete-file',
                        'aggregate' => request()->get('aggregate'),
                        'key' => $key,
                        'lang' => $lang,
                        'id' => $file->id
                    ]
                    : ['/config/admin/delete-file', 'key' => $key, 'lang' => $lang, 'id' => $file->id];
                $initialPreviewConfig[] = [
                    'key' => $file->id,
                    'url' => Url::to($url),
                    'width' => '120px',
                    'caption' => $fileName,
                ];
            }
        }

        return compact('initialPreview', 'initialPreviewConfig');
    }

    protected function getPreviewContent(File $file)
    {
        $result = null;
        $imagesExtensions = ['jpeg', 'jpg', 'png', 'gif', 'bmp', 'tiff', 'svg', 'exif', 'webp'];
        $videoExtensions = ['mp4', 'webm', 'mov', 'ogv', 'ogg', 'mkv'];
        if (in_array($file->extension, $imagesExtensions)) {
            $result = Html::img(FPM::originalSrc($file->id), [
                'class' => 'file-preview-image',
                'id' => 'preview-image-' . $file->id,
                'data-id' => $file->id
            ]);
        } elseif (in_array($file->extension, $videoExtensions)) {
            $src = Html::tag('source', null, ['src' => FPM::originalSrc($file->id)]);
            $result = Html::tag('video', $src, [
                'class' => 'file-preview-video',
                'id' => 'preview-video-' . $file->id,
                'width' => '200px',
                'controls' => true,
            ]);
        } else {
            $result = Html::tag('i', null,
                ['class' => 'glyphicon glyphicon-file', 'id' => 'preview-image-' . $file->id]);
            $result = Html::tag('div', $result, ['class' => 'file-preview-other']);
        }

        return $result;
    }

    private function otherButtons()
    {
        $html = '<a class="meta-btn btn btn-xs btn-default pull-right" data-toggle="modal" href="/imagesUpload/meta-data/generate-form/" {dataKey} data-lang="{lang}" data-target=".modal-hidden">
    <i class="glyphicon glyphicon-tag file-icon-large text-success"></i>
</a>';
        $lang = urlManager()->langParam . '=' . LanguageHelper::getEditLanguage();
        $html = preg_replace('{{lang}}', $lang, $html);
        return $html;
    }
}
