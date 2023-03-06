<?php

namespace common\modules\builder\blocks\home;

use common\helpers\MediaHelper;
use Yii;
use backend\components\FormBuilder;
use backend\modules\imagesUpload\widgets\imagesUpload\ImageUpload;
use common\modules\builder\models\BuilderModel;
use common\validators\FileRequiredValidator;

/**
 * Class HomeDownloadBlock
 * @package common\modules\builder\blocks\home
 */
class HomeDownloadBlock extends BuilderModel
{
    const BUILDER_DOWNLOAD_BLOCK_FILE = 'builder_home_download_block_file';
    const BUILDER_DOWNLOAD_BLOCK_IMAGE = 'builder_home_download_block_image';

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $content;

    /**
     * @var string
     */
    public $btn_label;

    /**
     * @var string
     */
    public $btn_link;

    /**
     * @var int
     */
    public $file;

    /**
     * @var int
     */
    public $image;

    /**
     * List of ALL attributes corresponding to $this model
     *
     * @return array
     */
    public function getBuilderAttributes(): array
    {
        return [
            'title',
            'content',
            'btn_label',
            'btn_link',
            'file',
            'image',
        ];
    }

    /**
     * List of all file attributes. MUST have the following syntax:
     * ```
     * return [
     *      ...,
     *      `attributeName` => self::ATTRIBUTE_FILE_CONSTANT,
     *      `image` => self::SAVE_ATTRIBUTE_IMAGE,
     *      ...
     * ];
     * ```
     *
     * @return array
     */
    public function getUploadAttributes(): array
    {

        return [
            'file' => self::BUILDER_DOWNLOAD_BLOCK_FILE,
            'image' => self::BUILDER_DOWNLOAD_BLOCK_IMAGE,
        ];
    }


    /**
     * Returns the validation rules for attributes.
     * The same as default {{rules()}} method.
     *
     * @return array
     */
    public function validationRules(): array
    {
        return [
            [['title'], 'required'],
            [['title'], 'string', 'max' => MAX_TEXT],
            [['content'], 'string', 'max' => MAX_TEXTAREA],
            [['btn_label'], 'string', 'max' => MAX_TEXT],
            [['btn_link'], 'string', 'max' => MAX_TEXT],
            [
                ['file'],
                FileRequiredValidator::class,
                'saveAttribute' => self::BUILDER_DOWNLOAD_BLOCK_FILE,
                'skipOnEmpty' => true
            ],
            [
                ['image'],
                FileRequiredValidator::class,
                'saveAttribute' => self::BUILDER_DOWNLOAD_BLOCK_IMAGE,
                'skipOnEmpty' => false
            ],
        ];
    }

    /**
     * Title for current builder block
     *
     * @return string
     */
    public static function getTitle(): string
    {
        return Yii::t('back/builder', 'Download block');
    }

    /**
     * Array of properties labels
     *
     * @return array
     * @see `attributeLabels()` in {ActiveRecord}
     */
    public function getAttributeLabels(): array
    {
        return [
            'title' => Yii::t('back/builder', 'Title'),
            'content' => Yii::t('back/builder', 'Content'),
            'btn_label' => Yii::t('back/builder', 'Button label'),
            'btn_link' => Yii::t('back/builder', 'Button link'),
            'file' => Yii::t('back/builder', 'File'),
            'image' => Yii::t('back/builder', 'Image'),
        ];
    }

    /**
     * Form data config. Passes to {FormBuilder} to generate form fields
     *
     * @return array
     * @throws \Exception
     * @see \common\modules\builder\widgets\DummyFormBuilder
     */
    public function getFormConfig(): array
    {
        return [
            'title' => ['type' => FormBuilder::INPUT_TEXT],
            'content' => ['type' => FormBuilder::INPUT_TEXTAREA],
            'btn_label' => ['type' => FormBuilder::INPUT_TEXT],
            'btn_link' => ['type' => FormBuilder::INPUT_TEXT],
            'file' => [
                'type' => FormBuilder::INPUT_WIDGET,
                'widgetClass' => ImageUpload::class,
                'options' => [
                    'model' => $this,
                    'attribute' => 'file',
                    'saveAttribute' => self::BUILDER_DOWNLOAD_BLOCK_FILE,
                    'allowedFileExtensions' => DOC_VALID_FORMATS,
                    'multiple' => false,
                    'maxFileSize' => MAX_DOC_KB
                ]
            ],
            'image' => [
                'type' => FormBuilder::INPUT_WIDGET,
                'widgetClass' => ImageUpload::class,
                'options' => [
                    'model' => $this,
                    'attribute' => 'image',
                    'saveAttribute' => self::BUILDER_DOWNLOAD_BLOCK_IMAGE,
                    'allowedFileExtensions' => IMAGE_VALID_FORMATS,
                    'multiple' => false,
                    'maxFileSize' => MAX_IMAGE_KB
                ]
            ],
            'target_sign' => [
                'type' => FormBuilder::INPUT_HIDDEN,
                'label' => false
            ]
        ];
    }

    /**
     * @return array
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function getApiAttributes(): array
    {
        return [
            'title' => $this->title,
            'content' => $this->content,
            'btn' => [
                'label' => $this->btn_label,
                'link' => $this->btn_link ?? formatter()->file((int)$this->file ?? null),
            ],
            'image' => formatter()->image($this->image)
        ];
    }
}
