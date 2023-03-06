<?php

namespace common\modules\config\domain\services;

use common\modules\config\domain\values\CKEditorInput;
use common\modules\config\domain\values\CtaTemplate;
use common\modules\config\domain\values\DateTimePicker;
use common\modules\config\domain\values\SelectizeInput;
use yii\helpers\ArrayHelper;
use common\helpers\LanguageHelper;
use common\modules\config\domain\values\Select;
use common\modules\config\domain\values\CodeInput;
use common\modules\config\domain\values\FileInput;
use common\modules\config\domain\values\TextInput;
use common\modules\config\domain\values\PhoneInput;
use common\modules\config\domain\values\RadioInput;
use common\modules\config\domain\values\EditorInput;
use common\modules\config\domain\values\HiddenInput;
use common\modules\config\domain\values\TextAreaInput;
use common\modules\config\domain\values\CheckboxInput;
use common\modules\config\domain\values\PasswordInput;
use common\modules\config\domain\values\RadioListInput;
use common\modules\config\domain\values\MultipleColumn;
use common\modules\config\infrastructure\values\IField;
use common\modules\config\domain\values\CheckboxListInput;
use common\modules\config\domain\values\DropdownListInput;
use common\modules\config\infrastructure\services\IFieldFactory;
use common\modules\config\domain\values\TitleInput;
/**
 * Class FieldFactory - responsible for fields classes instantiation.
 *
 * @author Bogdan K. Fedun <delagics@gmail.com>
 */
class FieldFactory implements IFieldFactory
{
    const INPUT_TEXT = 'text';
    const INPUT_TITLE = 'title';
    const INPUT_TEXTAREA = 'textarea';
    const INPUT_PASSWORD = 'password';
    const INPUT_HIDDEN = 'hidden';
    const INPUT_RADIO = 'radio';
    const INPUT_CHECKBOX = 'checkbox';
    const INPUT_FILE = 'file';
    const INPUT_EDITOR = 'editor';
    const INPUT_RADIO_LIST = 'radioList';
    const INPUT_DROPDOWN_LIST = 'dropdownList';
    const INPUT_CHECKBOX_LIST = 'checkboxList';
    const INPUT_PHONE = 'phone';
    const INPUT_CODE = 'code';
    const INPUT_MULTI_SELECT = 'multiSelect';
    const INPUT_SELECTIZE = 'selectize';
    const INPUT_MULTIPLE = 'multiple';
    const INPUT_CTA_TEMPLATE = 'ctaTemplate';
    const INPUT_CKEDITOR = 'ckeditor';
    const INPUT_DATETIMEPICKER = 'dateTimePicker';

    static $_fieldValueList = null;

    public function getTypesMap()
    {
        return [
            self::INPUT_TEXT => TextInput::class,
            self::INPUT_TITLE => TitleInput::class,
            self::INPUT_TEXTAREA => TextAreaInput::class,
            self::INPUT_PASSWORD => PasswordInput::class,
            self::INPUT_HIDDEN => HiddenInput::class,
            self::INPUT_RADIO => RadioInput::class,
            self::INPUT_CHECKBOX => CheckboxInput::class,
            self::INPUT_FILE => FileInput::class,
            self::INPUT_EDITOR => EditorInput::class,
            self::INPUT_CKEDITOR => CKEditorInput::class,
            self::INPUT_RADIO_LIST => RadioListInput::class,
            self::INPUT_DROPDOWN_LIST => DropdownListInput::class,
            self::INPUT_CHECKBOX_LIST => CheckboxListInput::class,
            self::INPUT_PHONE => PhoneInput::class,
            self::INPUT_CODE => CodeInput::class,
            self::INPUT_MULTI_SELECT => Select::class,
            self::INPUT_MULTIPLE => MultipleColumn::class,
            self::INPUT_CTA_TEMPLATE => CtaTemplate::class,
            self::INPUT_SELECTIZE => SelectizeInput::class,
            self::INPUT_DATETIMEPICKER => DateTimePicker::class,
        ];
    }

    public function make(string $name, array $specification, bool $aggregated = false): IField
    {
        $type = obtain('type', $specification);
        $label = obtain('label', $specification, '');
        $hint = obtain('hint', $specification, false);
        $description = obtain('description', $specification, '');
        $default = obtain('default', $specification, null);
        $value = obtain('value', $specification, null);
        $rules = obtain('rules', $specification, []);
        $options = obtain('options', $specification, []);
        $display = obtain('display', $specification, false);
        $autoload = obtain('autoload', $specification, false);
        $tab = obtain('tab', $specification, 'Main');

        if ($type === self::INPUT_FILE) {
            $fieldValue = $values[$name] ?? null;
            if ($fieldValue) {
                $options['required'] = false;
            }
        }

        /** @var IField $field */
        return createObject(
            $this->getFieldClass($type),
            [
                $name,
                $type,
                $label,
                $description,
                $default,
                $value,
                $rules,
                $options,
                $display,
                $autoload,
                $hint,
                $aggregated,
                $tab
            ]
        );
    }

    private function getFieldClass($type)
    {
        return obtain($type, $this->getTypesMap());
    }
}
