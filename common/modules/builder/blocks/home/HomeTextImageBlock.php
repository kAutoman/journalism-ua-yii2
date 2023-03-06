<?php

namespace common\modules\builder\blocks\home;

use backend\components\FormBuilder;
use backend\modules\imagesUpload\widgets\imagesUpload\ImageUpload;
use backend\widgets\Editor as EditorWidget;
use common\modules\builder\models\BuilderModel;
use common\validators\FileRequiredValidator;
use Yii;

/**
 * Class HomeTextImageBlock
 * @package common\modules\builder\blocks\home
 */
class HomeTextImageBlock extends BuilderModel
{
    const BUILDER_HOME_TEXT_IMAGE_IMAGE = 'builder_home_text_image_image';

    /**
     * @var string
     */
    public $title;

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
            'image' => self::BUILDER_HOME_TEXT_IMAGE_IMAGE,
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
            [['content'], 'string', 'max' => MAX_TEXTAREA],
            [
                ['image'],
                FileRequiredValidator::class,
                'saveAttribute' => self::BUILDER_HOME_TEXT_IMAGE_IMAGE,
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
        return Yii::t('back/builder', 'Heading member page');
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
            'content' => [
                'type' => FormBuilder::INPUT_WIDGET,
                'widgetClass' => EditorWidget::class,
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
                    'saveAttribute' => self::BUILDER_HOME_TEXT_IMAGE_IMAGE,
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
            'content' => $this->content,
            'image' => formatter()->image($this->image)
        ];
    }
}
