<?php

namespace common\modules\builder\blocks;

use Yii;
use common\validators\FileRequiredValidator;
use common\modules\builder\models\BuilderModel;
use backend\components\FormBuilder;
use backend\modules\imagesUpload\widgets\imagesUpload\ImageUpload;

/**
 * Class Video
 *
 * @property int $video
 * @property int $poster
 *
 * @package common\modules\builder\blocks
 */
class Video extends BuilderModel
{
    const BUILDER_VIDEO_VIDEO = 'builder_video_video';
    const BUILDER_VIDEO_POSTER = 'builder_video_poster';

    /**
     * @var int
     */
    public $video;
    /**
     * @var int
     */
    public $poster;

    /**
     * List of ALL attributes corresponding to $this model
     *
     * @return array
     */
    public function getBuilderAttributes(): array
    {
        return [
            'video',
            'poster'
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
            'video' => self::BUILDER_VIDEO_VIDEO,
            'poster' => self::BUILDER_VIDEO_POSTER,
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
            [['video'],
                FileRequiredValidator::class,
                'saveAttribute' => self::BUILDER_VIDEO_VIDEO,
                'skipOnEmpty' => false
            ],
            [['poster'],
                FileRequiredValidator::class,
                'saveAttribute' => self::BUILDER_VIDEO_POSTER,
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
        return Yii::t('back/builder', 'Video');
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
            'video' => Yii::t('back/builder', 'Video'),
            'poster' => Yii::t('back/builder', 'Poster'),
        ];
    }

    /**
     * Form data config. Passes to {FormBuilder} to generate form fields
     *
     * @return array
     * @see \common\modules\builder\widgets\DummyFormBuilder
     * @throws \Exception
     */
    public function getFormConfig(): array
    {
        return [
            'poster' => [
                'type' => FormBuilder::INPUT_WIDGET,
                'widgetClass' => ImageUpload::class,
                'options' => [
                    'model' => $this,
                    'attribute' => 'poster',
                    'saveAttribute' => self::BUILDER_VIDEO_POSTER,
                    'allowedFileExtensions' => IMAGE_VALID_FORMATS,
                    'multiple' => false,
                    'maxFileSize' => MAX_IMAGE_KB
                ]
            ],
            'video' => [
                'type' => FormBuilder::INPUT_WIDGET,
                'widgetClass' => ImageUpload::class,
                'options' => [
                    'model' => $this,
                    'attribute' => 'video',
                    'saveAttribute' => self::BUILDER_VIDEO_VIDEO,
                    'allowedFileExtensions' => VIDEO_VALID_FORMATS,
                    'multiple' => false,
                    'maxFileSize' => MAX_VIDEO_KB
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
            'video' => app()->getFormatter()->video($this->video, $this->poster)
        ];
    }
}
