<?php

namespace common\modules\faq\blocks;

use api\modules\faq\models\AskQuestionForm as APIForm;
use Yii;
use common\modules\builder\models\BuilderModel;
use common\modules\config\application\entities\AskQuestionForm;
use common\modules\config\application\components\AggregateMaker;
use backend\components\FormBuilder;

/**
 * Class AskQuestion
 *
 * @property string $title
 * @property string $description
 *
 * @package common\modules\faq\blocks
 */
class AskQuestion extends BuilderModel
{
    /**
     * @var string
     */
    public $title;
    /**
     * @var string
     */
    public $description;

    /**
     * List of ALL attributes corresponding to $this model
     *
     * @return array
     */
    public function getBuilderAttributes(): array
    {
        return [
            'title',
            'description'
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
            [['title'], 'string', 'max' => 100],
            [['description'], 'string', 'max' => 1000],
        ];
    }

    /**
     * Title for current builder block
     *
     * @return string
     */
    public static function getTitle(): string
    {
        return Yii::t('back/faq', 'Ask question widget');
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
            'title' => Yii::t('back/faq', 'Title'),
            'description' => Yii::t('back/faq', 'Description'),
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
            'description' => ['type' => FormBuilder::INPUT_TEXTAREA]
        ];
    }

    /**
     * Block attributes for API response.
     *
     * @return array
     */
    public function getApiAttributes(): array
    {
        $aggregate = new AggregateMaker(AskQuestionForm::class);
        /** @var AskQuestionForm $formConfig */
        $formConfig = $aggregate->make();
        return [
            'title' => $this->title,
            'description' => $this->description,
            'form' => [
                'fields' => [
                    [
                        'name' => 'name',
                        'type' => 'text',
                        'label' => $formConfig->formNameLabel,
                        'placeholder' => $formConfig->formNamePlaceholder,
                        'required' => true,
                    ],
                    [
                        'name' => 'email',
                        'type' => 'text',
                        'label' => $formConfig->formEmailLabel,
                        'placeholder' => $formConfig->formEmailPlaceholder,
                        'required' => true,
                    ],
                    [
                        'name' => 'phone',
                        'type' => 'text',
                        'label' => $formConfig->formPhoneLabel,
                        'placeholder' => $formConfig->formPhonePlaceholder,
                        'required' => false,
                    ],
                    [
                        'name' => 'question',
                        'type' => 'textarea',
                        'label' => $formConfig->formQuestionLabel,
                        'placeholder' => $formConfig->formQuestionPlaceholder,
                        'required' => true,
                    ],
                ],
                'submit' => app()->getFormatter()->submitButton($formConfig->formSubmitButton, APIForm::getSubmitUrl())
            ],
        ];
    }
}
