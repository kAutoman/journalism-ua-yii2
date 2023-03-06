<?php

namespace common\modules\builder\blocks\jury;

use Yii;
use backend\components\FormBuilder;
use backend\modules\imagesUpload\widgets\imagesUpload\ImageUpload;
use common\modules\builder\models\BuilderModel;
use common\validators\FileRequiredValidator;

/**
 * Class JuryHeaderBlock
 * @package common\modules\builder\blocks\jury
 */
class JuryHeaderBlock extends BuilderModel
{
    const BUILDER_JURY_HEADER_IMAGE = 'builder_jury_header_image';

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $sub_title = '';

    /**
     * @var string
     */
    public $content;

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
            'sub_title',
            'content',
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
            'image' => self::BUILDER_JURY_HEADER_IMAGE,
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
            [['title', 'content'], 'required'],
            [['title'], 'string', 'max' => MAX_TEXT],
            [['sub_title'], 'string', 'max' => MAX_TEXT],
            [['content'], 'string', 'max' => MAX_TEXTAREA],
            [
                ['image'],
                FileRequiredValidator::class,
                'saveAttribute' => self::BUILDER_JURY_HEADER_IMAGE,
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
        return Yii::t('back/builder', 'Heading jury page');
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
            'sub_title' => Yii::t('back/builder', 'Sub title'),
            'content' => Yii::t('back/builder', 'Content'),
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
//            'sub_title' => ['type' => FormBuilder::INPUT_TEXT],
            'content' => ['type' => FormBuilder::INPUT_TEXTAREA],
            'image' => [
                'type' => FormBuilder::INPUT_WIDGET,
                'widgetClass' => ImageUpload::class,
                'options' => [
                    'model' => $this,
                    'attribute' => 'image',
                    'saveAttribute' => self::BUILDER_JURY_HEADER_IMAGE,
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
     * Block attributes for API response.
     *
     * @return array
     */
    public function getApiAttributes(): array
    {
        return [
            'title' => $this->title,
//            'sub_title' => $this->sub_title,
            'content' => $this->content,
            'image' => formatter()->image((int)$this->image)
        ];
    }
}

