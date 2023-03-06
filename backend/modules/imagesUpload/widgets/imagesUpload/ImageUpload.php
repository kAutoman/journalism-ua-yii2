<?php

namespace backend\modules\imagesUpload\widgets\imagesUpload;

use common\helpers\LanguageHelper;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use kartik\file\FileInput;
use metalguardian\fileProcessor\helpers\FPM;
use common\models\EntityToFile;
use common\components\model\ActiveRecord;
use common\modules\builder\models\BuilderModel;
use backend\modules\imagesUpload\helpers\ImageUploadUrlHelper;
use backend\modules\imagesUpload\models\ImagesUploadModel;

/**
 * Class ImageUpload
 * @package backend\modules\imagesUpload\widgets\imagesUpload
 */
class ImageUpload extends Widget
{
    /**
     * @var ActiveRecord $model
     */
    public $model;

    /**
     * @var string $attribute
     */
    public $attribute;

    /**
     * @var string saveAttribute
     */
    public $saveAttribute = null;

    /**
     * @var bool
     */
    public $multiple = true;

    /**
     * @var float
     */
    public $aspectRatio = 0;

    /**
     * Allowed file extensions
     * @var array
     */
    public $allowedFileExtensions = ['png', 'jpg', 'jpeg'];

    /**
     * @var bool|int
     */
    public $maxFileSize = false;

    public $maxFileCount = 100;

    public $webp = true;

    /**
     * Url for rendering meta data form
     *
     * @var  string
     */
    public $renderMetaDataFormUrl = '/imagesUpload/meta-data/save-form/';

    /**
     * @var bool
     * @todo fix
     */
    public $showMetaDataBtn = true;

    private function getMaxFileCount()
    {
        return $this->multiple ? $this->maxFileCount : 1;
    }

    public function run()
    {
        if (!$this->model || !$this->attribute) {
            return null;
        }

        if ($this->model instanceof BuilderModel) {
            $uploadExtraData = $this->model->isNewRecord
                ? ['sign' => $this->model->target_sign]
                : ['id' => $this->model->id, 'sign' => $this->model->target_sign];
        } else {
            $uploadExtraData = $this->model->isNewRecord
                ? ['sign' => $this->model->sign]
                : ['id' => $this->model->id];
        }

        $allowedExtensions = $this->allowedFileExtensions;
        $extensionsAcceptMask = empty($this->allowedFileExtensions)
            ? '*'
            : call_user_func(function () use ($allowedExtensions) {
                $exts = [];
                // clone array, do not use existing one
                $exts = ArrayHelper::merge($exts, $allowedExtensions);
                array_walk($exts, function (&$item) {
                    $item = '.' . $item;
                });
                $result = implode(',', $exts);
                return $result;
            });
        $existModelImages = $this->collectExistingImages();

        $initialPreview = [];
        $initialPreviewConfig = [];
        /**
         * @var \common\models\EntityToFile $file
         */
        foreach ($existModelImages as $file) {
            $fileName = $file->file->base_name . '.' . $file->file->extension;
            $initialPreview[] = $this->getPreviewContent($file);
            $initialPreviewConfig[] = [
                'caption' => $fileName,
                'width' => '120px',
                'url' => ImagesUploadModel::deleteImageUrl(['id' => $file->id]),
                'key' => $file->id,
                'frameClass' => in_array($file->file->extension, static::getCropableImagesExtensions())
                    ? ''
                    : 'not-image',
            ];
        }

        $output = Html::hiddenInput('urlForSorting', ImagesUploadModel::sortImagesUrl(), ['id' => 'urlForSorting']);
        $output .= Html::hiddenInput('aspectRatio', $this->aspectRatio, ['class' => 'aspect-ratio']);
        $output .= Html::hiddenInput('isMultipleUpload', $this->multiple, ['class' => 'is-multiple-upload']);

        $index = $this->model->relModelIndex;
        $attribute = $index === null ? $this->attribute : "[$index]$this->attribute";
        $uploadUrl = ImagesUploadModel::uploadUrl([
            'model_name' => $this->model->className(),
            'attribute' => $attribute,
            'entity_attribute' => $this->saveAttribute,
            'webp' => $this->webp,
            'max_file_count' => $this->getMaxFileCount()
        ]);

        $cropableTypes = self::getCropableImagesTypes();

        $output .= FileInput::widget(
            [
                'model' => $this->model,
                'attribute' => $attribute,
                'options' => [
                    'multiple' => $this->multiple,
                    'accept' => $extensionsAcceptMask,
                ],
                'pluginOptions' => [
                    'dropZoneEnabled' => false,
                    'browseClass' => 'btn btn-success',
                    'browseIcon' => '<i class="glyphicon glyphicon-picture"></i> ',
                    'removeClass' => "btn btn-danger",
                    'removeIcon' => '<i class="glyphicon glyphicon-trash"></i> ',
                    'uploadClass' => "btn btn-info",
                    'uploadIcon' => '<i class="glyphicon glyphicon-upload"></i> ',
                    'uploadUrl' => $uploadUrl,
                    'allowedFileExtensions' => $this->allowedFileExtensions,
                    'allowedPreviewTypes' => ['image', 'video'],
                    // for all other types - preview will be with simple icon
                    'uploadExtraData' => $uploadExtraData,
                    'initialPreview' => $initialPreview,
                    'maxFileCount' => $this->getMaxFileCount(),
                    'initialPreviewConfig' => $initialPreviewConfig,
                    'maxFileSize' => $this->maxFileSize,
                    'overwriteInitial' => false,
                    'showRemove' => false,
                    'otherActionButtons' => $this->getOtherActionButtons(),
                    'fileActionSettings' => [
                        'indicatorSuccess' => $this->render('_success_buttons_template')
                    ],
                    'previewSettings' => $this->getPreviewSettings(),
                    'previewTemplates' => $this->getPreviewTemplates(),
                ],
                'pluginEvents' => [
                    'fileuploaded' => 'function(event, data, previewId, index) {
                       var elem = $("#"+previewId).find(".file-actions .file-footer-buttons .kv-file-remove");
                       var cropElem = $("#"+previewId).find(".file-actions .crop-link");
                       var metaDataElem = $("#"+previewId).find(".file-actions .meta-btn-ar");
                       var img = $("#"+previewId).find("img");
                       //id for cropped image replace
                       img.attr("id", "preview-image-"+data.response.imgId);

                       elem.attr("data-url", data.response.deleteUrl);
                       elem.attr("data-key", data.response.id);
                       cropElem.attr("href", data.response.cropUrl);
                       img.attr("src", data.response.url);
                       img.attr("data-id" , data.response.id);
                      
                      var container = $(this).closest(".file-input").parent();
                      container.removeClass("has-error");
                      container.find(".help-block").empty();
                       
                       if (metaDataElem.length !== 0) {
                       var metaHref = metaDataElem.attr("href");
                       metaHref = metaHref + data.response.imgId;
                       metaHref = metaHref + "?" + metaDataElem.data("lang");
                           metaDataElem.attr("href", metaHref );
                       }
                       
                       //fix file delete after uploading
                       elem.addClass("new-uploaded-image");
                       $(".file-upload-indicator .kv-file-remove").remove();
         
                       //Resort files
                       saveSort();

                       //Fix crop url for old images
                       fixMultiUploadImageCropUrl();
                       
                        //fix file index for correct file delete
                       $("#"+previewId).attr("data-fileindex", "init_"+$(this).data("fileindex"));
                    }',
                    'fileloaded' => "function(file, reader, previewId, index) {
                        //Fix url for old images
                        fixMultiUploadImageCropUrl();
                        
                        // Remove crop icon for non images
                        var cropableTypes = $cropableTypes;
                        if ($.inArray(reader.type, cropableTypes) === -1) {
                            $('#' + previewId).find('.crop-link').remove();
                        };
                    }",
                    'fileuploaderror' => "function(event, data, msg){
                            $('#' + data.id).remove();
                    }",
                ]
            ]
        );

        //$output .= '<br>';
        return $output;
    }

    /**
     * @param EntityToFile $file
     * @return null|string
     */
    protected function getPreviewContent(EntityToFile $file)
    {
        $imagesExtensions = ['jpeg', 'jpg', 'png', 'gif', 'bmp', 'tiff', 'svg', 'exif'];
        $videoExtensions = ['mp4', 'webm', 'mov', 'ogv', 'ogg', 'mkv'];
        $result = null;

        if (in_array($file->file->extension, $imagesExtensions)) {
            $result = Html::img(FPM::originalSrc($file->file_id), [
                'class' => 'file-preview-image',
                'id' => 'preview-image-' . $file->file_id,
                'data-id' => $file->file_id
            ]);
        } else {
            if (in_array($file->file->extension, $videoExtensions)) {
                $src = Html::tag('source', null, [
                    'src' => FPM::originalSrc($file->file_id)
                ]);
                $result = Html::tag('video', $src, [
                    'class' => 'file-preview-video',
                    'id' => 'preview-video-' . $file->file_id,
                    'width' => '200px',
                    'controls' => true,
                ]);
            } else {
                $result = Html::tag('i', null, [
                    'class' => 'glyphicon glyphicon-file',
                    'id' => 'preview-image-' . $file->file_id
                ]);
                $result = Html::tag('div', $result, [
                    'class' => 'file-preview-other',
                ]);
            }
        }

        return $result;
    }

    /**
     * Extensions that will be available for cropping by JS and will contain button for it
     *
     * @return array
     */
    protected static function getCropableImagesExtensions()
    {
        return ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
    }

    /**
     * @return string
     */
    protected static function getCropableImagesTypes()
    {
        $extensions = self::getCropableImagesExtensions();
        $types = [];
        // clone array, do not use existing one
        $types = ArrayHelper::merge($types, $extensions);
        array_walk($types, function (&$item) {
            $item = 'image/' . $item;
        });

        return Json::encode($types);
    }

    /**
     * @return array
     */
    protected function collectExistingImages()
    {
        $existModelImages = EntityToFile::find()->where('entity_model_name = :emn',
            [':emn' => $this->model->formName()]);
        if ($this->saveAttribute !== null) {
            $existModelImages->andWhere('attribute = :attr', [':attr' => $this->saveAttribute]);
        }

        if ($this->model instanceof BuilderModel) {
            $existModelImages = $this->model->isNewRecord
                ? $existModelImages->andWhere('temp_sign = :ts', [':ts' => $this->model->target_sign])
                : $existModelImages->andWhere('entity_model_id = :id', [':id' => $this->model->id]);
        } else {
            $existModelImages = $this->model->isNewRecord
                ? $existModelImages->andWhere('temp_sign = :ts', [':ts' => $this->model->sign])
                : $existModelImages->andWhere('entity_model_id = :id', [':id' => $this->model->id]);
        }
        $existModelImages = $existModelImages->orderBy('position DESC')->all();


        return $existModelImages;
    }

    /**
     * @return array
     */
    protected function getPreviewSettings()
    {
        return [
            'video' => [
                'width' => '200px',
                'height' => 'auto',
            ],
            'image' => [
                'width' => 'auto',
                'height' => 'auto',
            ],
            'text' => [
                'width' => '200px',
                'height' => '200px',
            ],
            'audio' => [
                'width' => '200px',
                'height' => '200px',
            ],
            'flash' => [
                'width' => '200px',
                'height' => '200px',
            ],
            'object' => [
                'width' => '200px',
                'height' => '200px',
            ],
            'other' => [
                'width' => '200px',
                'height' => '220px',
            ]
        ];
    }

    /**
     * @return array
     */
    protected function getPreviewTemplates()
    {
        return [
            'video' => '<div class="file-preview-frame{frameClass}" id="{previewId}" data-fileindex="{fileindex}"
                             title="{caption}">
                               <video width="{width}" height="{height}" controls>
                                   <source src="{data}" type="{type}">
                               </video>
                               {footer}
                            </div>',
            'image' => '<div class="file-preview-frame{frameClass}" id="{previewId}" data-fileindex="{fileindex}">
                            <img src="{data}" class="file-preview-image" title="{caption}" alt="{caption}" >
                            {footer}
                            </div>',
            'text' => '<div class="file-preview-frame{frameClass}" id="{previewId}" data-fileindex="{fileindex}">
                            <div class="file-preview-text" title="{caption}"
                            {data}
                            </div>
                            {footer}
                            </div>',
            'html' => '<div class="file-preview-frame{frameClass}" id="{previewId}" data-fileindex="{fileindex}">
                            <object data="{data}" type="{type}" width="{width}" height="{height}">
                                <div class="file-preview-other">
                                    {previewFileIcon}
                                </div>
                            </object>
                            {footer}
                            </div>',
            'audio' => '<div class="file-preview-frame{frameClass}" id="{previewId}" data-fileindex="{fileindex}"
                            title="{caption}">
                            <audio controls>
                            <source src="{data}" type="{type}">
                                <div class="file-preview-other">
                                    {previewFileIcon}
                                </div>
                            </audio>
                            {footer}
                            </div>',
            'other' => '<div class="file-preview-frame{frameClass}" id="{previewId}" data-fileindex="{fileindex}"
                            title="{caption}">
                                <div class="file-preview-other">
                                    {previewFileIcon}
                                </div>
                            {footer}
                            </div>',
            'generic' => '<div class="file-preview-frame{frameClass}" id="{previewId}" data-fileindex="{fileindex}">
                            {content}
                            {footer}
                            </div>',
        ];
    }

    private function getOtherActionButtons()
    {
        if ($this->showMetaDataBtn) {
            return $this->render('_meta_data_btn', [
                'url' => $this->renderMetaDataFormUrl
                    ?: ImageUploadUrlHelper::getMetaDataFormGenerateUrl(['id' => '']),
                'lang' => urlManager()->langParam . '=' . LanguageHelper::getEditLanguage()
            ]);
        }

        return '';//$this->render('_crop_button');
    }
}
