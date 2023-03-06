<?php

namespace common\modules\builder\blocks;

use Yii;
use common\modules\builder\models\BuilderModel;
use backend\components\FormBuilder;
use backend\widgets\Editor;
use backend\modules\imagesUpload\widgets\imagesUpload\ImageUpload;

/**
 * Class HeadingTextAreaButton
 *
 * @property string $title
 * @property string $content
 * @property string $button_label
 * @property string $button_link
 *
 * @package common\modules\builder\blocks
 */
class HeadingTextAreaButton extends BuilderModel
{

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
    public $button_label;

    /**
     * @var string
     */
    public $button_link;

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
            'button_label',
            'button_link',
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
            [['title', 'button_label', 'button_link'], 'string', 'max' => MAX_TEXT],
            [['content'], 'string', 'max' => MAX_TEXTAREA],
            [['button_label', 'button_link'], 'required']
        ];
    }

    /**
     * Title for current builder block
     *
     * @return string
     */
    public static function getTitle(): string
    {
        return Yii::t('back/builder', 'Heading with text area and button link');
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
            'button_label' => Yii::t('back/builder', 'Button label'),
            'button_link' => Yii::t('back/builder', 'Button link'),
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
            'title' => ['type' => FormBuilder::INPUT_TEXT],
            'content' => ['type' => FormBuilder::INPUT_TEXTAREA],
            'button_label' => ['type' => FormBuilder::INPUT_TEXT],
            'button_link' => ['type' => FormBuilder::INPUT_TEXT],
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
            'content' => nl2br($this->content),
            'button' => formatter()->link($this->button_label, $this->button_link)
        ];
    }
}
