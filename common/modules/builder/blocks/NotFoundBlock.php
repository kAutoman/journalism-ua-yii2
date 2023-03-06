<?php

namespace common\modules\builder\blocks;

use Yii;
use backend\components\FormBuilder;
use common\modules\builder\models\BuilderModel;

/**
 * Class NotFoundBlock
 * @package common\modules\builder\blocks
 */
class NotFoundBlock extends BuilderModel
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $message;

    /**
     * @var string
     */
    public $btn_label;

    /**
     * List of ALL attributes corresponding to $this model
     *
     * @return array
     */
    public function getBuilderAttributes(): array
    {
        return [
            'title',
            'message',
            'btn_label',
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

            [['message'], 'required'],
            [['message'], 'string', 'max' => MAX_TEXTAREA],

            [['btn_label'], 'required'],
            [['btn_label'], 'string', 'max' => MAX_TEXT],
        ];
    }

    /**
     * Title for current builder block
     *
     * @return string
     */
    public static function getTitle(): string
    {
        return Yii::t('back/builder', 'Not found block');
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
            'message' => Yii::t('back/builder', 'Message'),
            'btn_label' => Yii::t('back/builder', 'Button label'),
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
            'message' => ['type' => FormBuilder::INPUT_TEXTAREA],
            'btn_label' => ['type' => FormBuilder::INPUT_TEXT],
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
            'message' => $this->message,
            'btn' => [
                'label' => $this->btn_label,
            ],
        ];
    }
}
