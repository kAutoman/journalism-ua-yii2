<?php

namespace common\modules\builder\models;

use backend\components\FormBuilder;
use backend\modules\imagesUpload\widgets\imagesUpload\ImageUpload;
use backend\widgets\Editor;
use common\components\TagLevel;
use common\modules\config\application\validators\InputTagValidator;
use common\validators\FileRequiredValidator;
use common\widgets\InputTagWidget;

/**
 * Class SampleModel
 *
 * @package common\modules\builder\models
 */
class SampleModel extends BuilderModel
{
    const IMAGE_ATTRIBUTE = 'image_attribute';
    const IMAGES_ATTRIBUTE = 'images_attribute';

    /**
     * @var string
     */
    public $label;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $content;

    /**
     * @var int
     */
    public $image;

    /**
     * @var string
     */
    public $images;

    /**
     * List of ALL attributes corresponding to $this model
     *
     * @return array
     */
    public function getBuilderAttributes(): array
    {
        return [
            'label',
            'description',
            'content',
            'image',
            'images'
        ];
    }

    public function getArrayAttributes(): array
    {
        return [];
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
            'image' => self::IMAGE_ATTRIBUTE,
            'images' => self::IMAGES_ATTRIBUTE,
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
            [['label', 'content', 'description'], 'string'],
            [['label'], 'required'],
            [['images'], 'safe'],
            [['image'],
                FileRequiredValidator::class,
                'saveAttribute' => self::IMAGE_ATTRIBUTE,
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
        return 'Sample builder block';
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
            'label' => 'Label',
            'description' => 'Description',
            'content' => 'Content',
            'image' => 'Image',
            'images' => 'Images',
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
            'label' => [
                'type' => FormBuilder::INPUT_TEXT
            ],
            'description' => [
                'type' => FormBuilder::INPUT_TEXTAREA
            ],
            'content' => [
                'type' => FormBuilder::INPUT_WIDGET,
                'widgetClass' => Editor::class,
                'widgetOptions' => [
                    'model' => $this,
                    'attribute' => 'content'
                ]
            ],
            'image' => [
                'type' => FormBuilder::INPUT_WIDGET,
                'widgetClass' => ImageUpload::class,
                'options' => [
                    'model' => $this,
                    'attribute' => 'image',
                    'saveAttribute' => self::IMAGE_ATTRIBUTE,
                    'multiple' => false,
                    'allowedFileExtensions' => ['jpg', 'jpeg'],
                    'webp' => true,
                    'showMetaDataBtn' => true,
                ],

            ],
            'images' => [
                'type' => FormBuilder::INPUT_WIDGET,
                'widgetClass' => ImageUpload::class,
                'options' => [
                    'model' => $this,
                    'attribute' => 'images',
                    'saveAttribute' => self::IMAGES_ATTRIBUTE,
                    'multiple' => true,
                    'allowedFileExtensions' => ['jpg', 'jpeg'],
                    'webp' => true,
                    'showMetaDataBtn' => true,
                ],

            ],
            'target_sign' => [
                'type' => FormBuilder::INPUT_HIDDEN,
                'label' => false
            ]
        ];
    }
}
