<?php

namespace common\modules\config\application\entities;

use common\modules\config\domain\aggregates\ConfigAggregate;
use common\modules\config\domain\services\FieldFactory;

/**
 * Class CompetitionRequestr
 * @package common\modules\config\application\entities
 *
 * @property string $formStep1Label
 * @property string $formStep1Step
 * @property string $formStep1FormLabel
 * @property string $formStep1BtnLabel
 *
 * @property string $formStep2Label
 * @property string $formStep2Step
 * @property string $formStep2FormLabel
 * @property string $formStep2BtnLabel
 *
 * @property string $formStep1FormNameLabel
 * @property string $formStep1FormNameHint
 * @property string $formStep1FormEmailLabel
 * @property string $formStep1FormEmailHint
 * @property string $formStep1FormGenderLabel
 * @property string $formStep1FormGenderHint
 * @property string $formStep1FormAgeLabel
 * @property string $formStep1FormAgeHint
 * @property string $formStep1FormCityLabel
 * @property string $formStep1FormCityHint
 * @property string $formStep1FormPositionLabel
 * @property string $formStep1FormPositionHint
 * @property string $formStep1FormCompanyNameLabel
 * @property string $formStep1FormCompanyNameHint
 * @property string $formStep1FormPhoneLabel
 * @property string $formStep1FormPhoneHint
 * @property string $formStep1FormExperienceLabel
 * @property string $formStep1FormExperienceHint
 *
 * @property string $formStep2FormOtherNameLabel
 * @property string $formStep2FormOtherNameHint
 * @property string $formStep2FormMaterialLabelLabel
 * @property string $formStep2FormMaterialLabelHint
 * @property string $formStep2FormMaterialTypeLabel
 * @property string $formStep2FormMaterialTypeHint
 * @property string $formStep2FormProgramLabelLabel
 * @property string $formStep2FormProgramLabelHint
 * @property string $formStep2FormProgramPublishedDateLabel
 * @property string $formStep2FormProgramPublishedDateHint
 * @property string $formStep2FormProgramLinkLabel
 * @property string $formStep2FormProgramLinkHint
 * @property string $formStep2FormNominationLabel
 * @property string $formStep2FormNominationHint
 * @property string $formStep2FormArgumentLabel
 * @property string $formStep2FormArgumentHint
 * @property string $formStep2FormAwardsLabel
 * @property string $formStep2FormAwardsHint
 *
 * @property string $formSuccessLabel
 * @property string $formSuccessText
 */
class CompetitionRequest extends ConfigAggregate
{
    /**
     * Defines specification for current config entity aggregate.
     * Note: all keys will be auto prefixed with aggregate root name.
     *
     * @return array
     */
    public function specifications(): array
    {
        return [
            bt('Step 1', 'form') => [
                'form.step1.label' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Label',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 255],
                    ],
                ],
                'form.step1.step' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Step label',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 255],
                    ],
                ],
                'form.step1.form.label' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Form label',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 255],
                    ],
                ],
                'form.step1.btn.label' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Button label',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 255],
                    ],
                ],
            ],
            bt('Step 2', 'form') => [
                'form.step2.label' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Label',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 255],
                    ],
                ],
                'form.step2.step' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Step label',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 255],
                    ],
                ],
                'form.step2.form.label' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Form label',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 255],
                    ],
                ],
                'form.step2.btn.label' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Button label',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 255],
                    ],
                ],
            ],
            bt('Step 1 fields', 'form') => $this->step1Fields(),
            bt('Step 2 fields', 'form') => $this->step2Fields(),
            bt('Success popup', 'form') => [
                'form.success.label' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Label',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 255],
                    ],
                ],
                'form.success.text' => [
                    'type' => FieldFactory::INPUT_TEXTAREA,
                    'label' => 'Text',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['string', 'max' => 5000],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function step1Fields(): array
    {
        $fields = [
            'name' => 'Name',
            'email' => 'Email',
            'gender' => 'Gender',
            'age' => 'Age',
            'city' => 'City',
            'position' => 'Position',
            'company_name' => 'Vompany name',
            'phone' => 'Phone',
            'experience' => 'Experience',
        ];

        $items = [];

        foreach ($fields as $field => $label) {
            if (strpos($field, '_')) {
                $field = str_replace('_', '.', $field);
            }

            $items["form.step1.form.{$field}.label"] = [
                'type' => FieldFactory::INPUT_TEXT,
                'label' => $label . ' label',
                'default' => null,
                'display' => true,
                'autoload' => true,
                'rules' => [
                    ['required'],
                    ['string', 'max' => 255],
                ],
            ];
            $items["form.step1.form.{$field}.hint"] = [
                'type' => FieldFactory::INPUT_TEXT,
                'label' => $label . ' hint',
                'default' => null,
                'display' => true,
                'autoload' => true,
                'rules' => [
                    ['required'],
                    ['string', 'max' => 255],
                ],
            ];
        }

        return $items;
    }

    /**
     * @return array
     */
    protected function step2Fields(): array
    {
        $fields = [
            'other_name' => 'Other name',
            'material_label' => 'Material label',
            'material_type' => 'Material type',
            'program_label' => 'Program label',
            'program_published_date' => 'Program published date',
            'program_link' => 'Program link',
            'nomination' => 'Nomination',
            'argument' => 'Argument',
            'awards' => 'Awards'
        ];

        $items = [];

        foreach ($fields as $field => $label) {
            if (strpos($field, '_')) {
                $field = str_replace('_', '.', $field);
            }

            $items["form.step2.form.{$field}.label"] = [
                'type' => FieldFactory::INPUT_TEXT,
                'label' => $label . ' label',
                'default' => null,
                'display' => true,
                'autoload' => true,
                'rules' => [
                    ['required'],
                    ['string', 'max' => 255],
                ],
            ];
            $items["form.step2.form.{$field}.hint"] = [
                'type' => FieldFactory::INPUT_TEXT,
                'label' => $label . ' hint',
                'default' => null,
                'display' => true,
                'autoload' => true,
                'rules' => [
                    ['required'],
                    ['string', 'max' => 255],
                ],
            ];
        }


        return $items;
    }
}
