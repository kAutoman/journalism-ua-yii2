<?php

namespace common\modules\builder\blocks\submit;

use common\models\CompetitionRequest as CommonCompetitionRequest;
use common\modules\config\application\components\AggregateMaker;
use common\modules\config\application\entities\CompetitionRequest;
use Yii;
use backend\components\FormBuilder;
use common\modules\builder\models\BuilderModel;
use yii\helpers\Url;

/**
 * Class FormSubmitBlock
 * @package common\modules\builder\blocks\submit
 */
class FormSubmitBlock extends BuilderModel
{

    /**
     * List of ALL attributes corresponding to $this model
     *
     * @return array
     */
    public function getBuilderAttributes(): array
    {
        return [
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
        ];
    }

    /**
     * Title for current builder block
     *
     * @return string
     */
    public static function getTitle(): string
    {
        return Yii::t('back/builder', 'Form block');
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
        ];
    }

    /**
     * Block attributes for API response.
     *
     * @return array
     */
    public function getApiAttributes(): array
    {
        /** @var CompetitionRequest $formConfig */
        $formConfig = (new AggregateMaker(CompetitionRequest::class))->make();

        return [
            'form' => [
                'step1' => [
                    'label' => $formConfig->formStep1Label,
                    'step' => $formConfig->formStep1Step,
                    'form_label' => $formConfig->formStep1FormLabel,
                    'fields' => [
                        [
                            'name' => 'name',
                            'type' => 'text',
                            'label' => $formConfig->formStep1FormNameLabel,
                            'hint' => $formConfig->formStep1FormNameHint,
                            'required' => true,
                        ],
                        [
                            'name' => 'email',
                            'type' => 'text',
                            'label' => $formConfig->formStep1FormEmailLabel,
                            'hint' => $formConfig->formStep1FormEmailHint,
                            'required' => true,
                        ],
                        [
                            'name' => 'gender_id',
                            'type' => 'dropdownlist',
                            'label' => $formConfig->formStep1FormGenderLabel,
                            'hint' => $formConfig->formStep1FormGenderHint,
                            'items' => CommonCompetitionRequest::getGenders(),
                            'required' => true,
                        ],
                        [
                            'name' => 'age',
                            'type' => 'text',
                            'label' => $formConfig->formStep1FormAgeLabel,
                            'hint' => $formConfig->formStep1FormAgeHint,
                            'required' => true,
                        ],
                        [
                            'name' => 'city',
                            'type' => 'text',
                            'label' => $formConfig->formStep1FormCityLabel,
                            'hint' => $formConfig->formStep1FormCityHint,
                            'required' => true,
                        ],
                        [
                            'name' => 'position',
                            'type' => 'text',
                            'label' => $formConfig->formStep1FormPositionLabel,
                            'hint' => $formConfig->formStep1FormPositionHint,
                            'required' => true,
                        ],
                        [
                            'name' => 'company_name',
                            'type' => 'text',
                            'label' => $formConfig->formStep1FormCompanyNameLabel,
                            'hint' => $formConfig->formStep1FormCompanyNameHint,
                            'required' => true,
                        ],
                        [
                            'name' => 'phone',
                            'type' => 'text',
                            'label' => $formConfig->formStep1FormPhoneLabel,
                            'hint' => $formConfig->formStep1FormPhoneHint,
                            'required' => true,
                        ],
                        [
                            'name' => 'experience',
                            'type' => 'text',
                            'label' => $formConfig->formStep1FormExperienceLabel,
                            'hint' => $formConfig->formStep1FormExperienceHint,
                            'required' => true,
                        ],
                    ],
                    'btn_label' => $formConfig->formStep1BtnLabel,
                ],
                'step2' => [
                    'label' => $formConfig->formStep2Label,
                    'step' => $formConfig->formStep2Step,
                    'form_label' => $formConfig->formStep2FormLabel,
                    'fields' => [
                        [
                            'name' => 'other_name',
                            'type' => 'text',
                            'label' => $formConfig->formStep2FormOtherNameLabel,
                            'hint' => $formConfig->formStep2FormOtherNameHint,
                            'required' => true,
                        ],
                        [
                            'name' => 'material_label',
                            'type' => 'text',
                            'label' => $formConfig->formStep2FormMaterialLabelLabel,
                            'hint' => $formConfig->formStep2FormMaterialLabelHint,
                            'required' => true,
                        ],
                        [
                            'name' => 'material_type_id',
                            'type' => 'dropdownlist',
                            'label' => $formConfig->formStep2FormMaterialTypeLabel,
                            'hint' => $formConfig->formStep2FormMaterialTypeHint,
                            'items' => CommonCompetitionRequest::getMaterialTypes(),
                            'required' => true,
                        ],
                        [
                            'name' => 'program_label',
                            'type' => 'text',
                            'label' => $formConfig->formStep2FormProgramLabelLabel,
                            'hint' => $formConfig->formStep2FormProgramLabelHint,
                            'required' => true,
                        ],
                        [
                            'name' => 'program_published_date',
                            'type' => 'text',
                            'label' => $formConfig->formStep2FormProgramPublishedDateLabel,
                            'hint' => $formConfig->formStep2FormProgramPublishedDateHint,
                            'required' => true,
                        ],
                        [
                            'name' => 'program_link',
                            'type' => 'text',
                            'label' => $formConfig->formStep2FormProgramLinkLabel,
                            'hint' => $formConfig->formStep2FormProgramLinkHint,
                            'required' => true,
                        ],
                        [
                            'name' => 'nomination_id',
                            'type' => 'dropdownlist',
                            'label' => $formConfig->formStep2FormNominationLabel,
                            'hint' => $formConfig->formStep2FormNominationHint,
                            'items' => CommonCompetitionRequest::getNominations(),
                            'required' => true,
                        ],
                        [
                            'name' => 'argument',
                            'type' => 'text',
                            'label' => $formConfig->formStep2FormArgumentLabel,
                            'hint' => $formConfig->formStep2FormArgumentHint,
                            'required' => true,
                        ],
                        [
                            'name' => 'awards',
                            'type' => 'text',
                            'label' => $formConfig->formStep2FormAwardsLabel,
                            'hint' => $formConfig->formStep2FormAwardsHint,
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'success' => [
                'label' => $formConfig->formSuccessLabel,
                'text' => $formConfig->formSuccessText,
            ],
            'submit' => app()->getFormatter()->submitButton($formConfig->formStep2BtnLabel, Url::toRoute([
                '/request/submit/submit-request'
            ]))
        ];
    }
}

