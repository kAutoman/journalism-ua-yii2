<?php

namespace common\modules\builder\blocks;

use Exception;
use Yii;
use backend\components\FormBuilder;
use common\validators\FileRequiredValidator;
use common\modules\builder\models\BuilderModel;
use backend\modules\imagesUpload\widgets\imagesUpload\ImageUpload;

/**
 * Class Images
 *
 * @property int $images
 *
 * @package common\modules\builder\blocks
 */
class Images extends BuilderModel
{
    const BUILDER_IMAGES_IMAGES = 'builder_images_images';

    /**
     * @var int
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
            'images',
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
            'images' => self::BUILDER_IMAGES_IMAGES,
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
            [
                ['images'],
                FileRequiredValidator::class,
                'saveAttribute' => self::BUILDER_IMAGES_IMAGES,
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
        return Yii::t('back/builder', 'Images');
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
            'images' => Yii::t('back/builder', 'Images'),
        ];
    }

    /**
     * Form data config. Passes to {FormBuilder} to generate form fields
     *
     * @return array
     * @throws Exception
     * @see \common\modules\builder\widgets\DummyFormBuilder
     */
    public function getFormConfig(): array
    {
        return [
            'images' => [
                'type' => FormBuilder::INPUT_WIDGET,
                'widgetClass' => ImageUpload::class,
                'options' => [
                    'model' => $this,
                    'attribute' => 'images',
                    'saveAttribute' => self::BUILDER_IMAGES_IMAGES,
                    'allowedFileExtensions' => IMAGE_VALID_FORMATS,
                    'multiple' => true,
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
     * Block attributes for API response.
     *
     * @return array
     */
    public function getApiAttributes(): array
    {
        return [
            'images' => formatter()->images($this->images)
        ];
    }
}
