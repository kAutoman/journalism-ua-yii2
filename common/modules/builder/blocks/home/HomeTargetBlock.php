<?php

namespace common\modules\builder\blocks\home;

use Yii;
use backend\components\FormBuilder;
use backend\modules\imagesUpload\widgets\imagesUpload\ImageUpload;
use backend\widgets\Editor as EditorWidget;
use common\modules\builder\models\BuilderModel;
use common\validators\FileRequiredValidator;

/**
 * Class HomeTargetBlock
 * @package common\modules\builder\blocks\home
 */
class HomeTargetBlock extends BuilderModel
{
    const BUILDER_HOME_TEXT_IMAGE_IMAGE = 'builder_home_target_block_image';

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $left_text;

    /**
     * @var string
     */
    public $right_text;

    /**
     * @var int
     */
    public $image;

    /**
     * @var string
     */
    public $content;

    /**
     * List of ALL attributes corresponding to $this model
     *
     * @return array
     */
    public function getBuilderAttributes(): array
    {
        return [
            'title',
            'left_text',
            'right_text',
            'image',
            'content',
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
            [['title', 'left_text', 'right_text', 'content'], 'required'],

            [['title'], 'string', 'max' => MAX_TEXT],

            [['left_text'], 'string', 'max' => MAX_TEXT],
            [['right_text'], 'string', 'max' => MAX_TEXT],

            [
                ['image'],
                FileRequiredValidator::class,
                'saveAttribute' => self::BUILDER_HOME_TEXT_IMAGE_IMAGE,
                'skipOnEmpty' => false
            ],

            [['content'], 'string', 'max' => MAX_EDITOR],
        ];
    }

    /**
     * Title for current builder block
     *
     * @return string
     */
    public static function getTitle(): string
    {
        return Yii::t('back/builder', 'Heading target block');
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
            'left_text' => Yii::t('back/builder', 'Left text'),
            'right_text' => Yii::t('back/builder', 'Right text'),
            'image' => Yii::t('back/builder', 'Image'),
            'content' => Yii::t('back/builder', 'Content'),
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
            'left_text' => ['type' => FormBuilder::INPUT_TEXT],
            'right_text' => ['type' => FormBuilder::INPUT_TEXT],
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
            'content' => [
                'type' => FormBuilder::INPUT_WIDGET,
                'widgetClass' => EditorWidget::class,
                'widgetOptions' => [
                    'model' => $this,
                    'attribute' => 'content'
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
            'left_text' => $this->left_text,
            'right_text' => $this->right_text,
            'image' => formatter()->image($this->image),
            'content' => $this->content,
        ];
    }
}
