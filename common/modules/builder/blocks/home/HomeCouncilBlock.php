<?php

namespace common\modules\builder\blocks\home;

use backend\modules\imagesUpload\widgets\imagesUpload\ImageUpload;
use backend\widgets\Editor as EditorWidget;
use common\models\HomeCouncilItem;
use common\validators\FileRequiredValidator;
use Yii;
use backend\components\FormBuilder;
use common\modules\builder\models\BuilderModel;

/**
 * Class HomeCouncilBlock
 * @package common\modules\builder\blocks\home
 */
class HomeCouncilBlock extends BuilderModel
{
    const BUILDER_HOME_COUNCIL_BLOCK_FILE = 'builder_home_council_block_file';

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
    public $after;

    public $file_label;

    public $file;

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
            'after',
            'file_label',
            'file',
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
            'file' => self::BUILDER_HOME_COUNCIL_BLOCK_FILE,
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

            [['content'], 'string', 'max' => MAX_EDITOR],

//            [['after'], 'string', 'max' => MAX_EDITOR],
//
//            [['file_label'], 'string', 'max' => MAX_TEXT],
//
//            [
//                ['file'],
//                FileRequiredValidator::class,
//                'saveAttribute' => self::BUILDER_HOME_COUNCIL_BLOCK_FILE,
//                'skipOnEmpty' => true
//            ],
        ];
    }

    /**
     * Title for current builder block
     *
     * @return string
     */
    public static function getTitle(): string
    {
        return Yii::t('back/builder', 'Council block');
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
            'content' => Yii::t('back/builder', 'Before content'),
            'after' => Yii::t('back/builder', 'After content'),
            'file_label' => Yii::t('back/builder', 'File label'),
            'file' => Yii::t('back/builder', 'File'),
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
//            'after' => [
//                'type' => FormBuilder::INPUT_WIDGET,
//                'widgetClass' => EditorWidget::class,
//                'widgetOptions' => [
//                    'model' => $this,
//                    'attribute' => 'after'
//                ]
//            ],
//            'file_label' => ['type' => FormBuilder::INPUT_TEXT],
//            'file' => [
//                'type' => FormBuilder::INPUT_WIDGET,
//                'widgetClass' => ImageUpload::class,
//                'options' => [
//                    'model' => $this,
//                    'attribute' => 'file',
//                    'saveAttribute' => self::BUILDER_HOME_COUNCIL_BLOCK_FILE,
//                    'allowedFileExtensions' => DOC_VALID_FORMATS,
//                    'multiple' => false,
//                    'maxFileSize' => MAX_DOC_KB
//                ]
//            ],
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
            'items' => $this->getItemsList(),
//            'after' => $this->after,
//            'file' => [
//                'label' => $this->file_label,
//                'link' => formatter()->file((int)$this->file ?? null),
//            ],
        ];
    }

    /**
     * @return array
     */
    protected function getItemsList(): array
    {
        /** @var HomeCouncilItem[] $models */
        $models = HomeCouncilItem::find()
            ->isPublished()
            ->orderBy(['position' => SORT_ASC])
            ->all();

        return array_map(function (HomeCouncilItem $model) {
            return [
                'label' => $model->label,
                'description' => $model->description,
                'photo' => formatter()->image($model->photoSrc->file_id ?? 0),
            ];
        }, $models);
    }
}
