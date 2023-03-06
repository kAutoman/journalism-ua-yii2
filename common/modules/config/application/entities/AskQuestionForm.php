<?php

namespace common\modules\config\application\entities;

use common\modules\config\domain\services\FieldFactory;
use common\modules\config\domain\aggregates\ConfigAggregate;

/**
 * Class FaqAskQuestion
 *
 * @property string $formNameLabel
 * @property string $formNamePlaceholder
 * @property string $formEmailLabel
 * @property string $formEmailPlaceholder
 * @property string $formPhoneLabel
 * @property string $formPhonePlaceholder
 * @property string $formQuestionLabel
 * @property string $formQuestionPlaceholder
 * @property string $formSubmitButton
 * @property string $successTitle
 * @property string $successDescription
 *
 * @package common\modules\config\application\entities
 */
class AskQuestionForm extends ConfigAggregate
{
    /**
     * Defines specification for current config entity aggregate.
     * Note: all keys will be auto prefixed with aggregate root name.
     * @return array
     */
    public function specifications(): array
    {
        return [
            bt('Fields', 'ask-question-form') => [
                'form.name.label' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Name label',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 255],
                    ],
                ],
                'form.name.placeholder' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Name placeholder',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['string', 'max' => 255],
                    ],
                ],
                'form.email.label' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Email label',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 255],
                    ],
                ],
                'form.email.placeholder' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Email placeholder',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['string', 'max' => 255],
                    ],
                ],
                'form.phone.label' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Phone label',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 255],
                    ],
                ],
                'form.phone.placeholder' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Phone placeholder',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['string', 'max' => 255],
                    ],
                ],
                'form.question.label' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Question label',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 255],
                    ],
                ],
                'form.question.placeholder' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Question placeholder',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['string', 'max' => 255],
                    ],
                ],
                'form.submit.button' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Submit button',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 255],
                    ],
                ],
            ],
            bt('Success', 'ask-question-form') => [
                'success.title' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Success title',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 255],
                    ],
                ],
                'success.description' => [
                    'type' => FieldFactory::INPUT_TEXTAREA,
                    'label' => 'Success description',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['string', 'max' => 500],
                    ],
                ],
            ],
        ];
    }

}
