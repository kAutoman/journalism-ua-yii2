<?php

namespace backend\components;

use common\modules\dynamicForm\components\Model;
use common\modules\dynamicForm\interfaces\DynamicFormInterface;
use common\modules\dynamicForm\widgets\DynamicForm;
use Yii;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveField;
use yii\widgets\InputWidget;
use yii\base\InvalidConfigException;
use common\components\model\ActiveRecord;

/**
 * Class FormBuilder
 *
 * @package backend\components
 */
class FormBuilder extends ActiveForm
{

    const INPUT_HIDDEN = 'hiddenInput';
    const INPUT_TEXT = 'textInput';
    const INPUT_TEXTAREA = 'textarea';
    const INPUT_PASSWORD = 'passwordInput';
    const INPUT_DROPDOWN_LIST = 'dropdownList';
    const INPUT_LIST_BOX = 'listBox';
    const INPUT_CHECKBOX = 'checkbox';
    const INPUT_RADIO = 'radio';
    const INPUT_CHECKBOX_LIST = 'checkboxList';
    const INPUT_RADIO_LIST = 'radioList';
    const INPUT_FILE = 'fileInput';
    const INPUT_HTML5 = 'input';
    const INPUT_WIDGET = 'widget';
    const INPUT_RAW = 'raw';
    const INPUT_DYNAMIC_FORM = 'dynamicForm';

    /**
     * @param ActiveRecord $model
     * @param array $config
     *
     * @return null|string
     * @throws InvalidConfigException
     */
    public function renderForm(ActiveRecord $model, array $config)
    {
        $form = null;
        foreach ($config as $attribute => $options) {
            $form .= $this->renderField($model, $attribute, $options);
        }

        return $form;
    }


    /**
     * @param ActiveRecord $model
     * @param string $attribute
     * @param array $settings
     *
     * @return ActiveField
     * @throws InvalidConfigException
     */
    public function renderField(ActiveRecord $model, $attribute, array $settings = [])
    {
        $fieldOptions = ArrayHelper::getValue($settings, 'fieldOptions', []);
        $field = $this->field($model, $attribute, $fieldOptions);

        if (($label = ArrayHelper::getValue($settings, 'label')) !== null) {
            $field->label($label, ArrayHelper::getValue($settings, 'labelOptions', []));
        }
        if (($hint = ArrayHelper::getValue($settings, 'hint')) !== null) {
            $field->hint($hint, ArrayHelper::getValue($settings, 'hintOptions', []));
        }

        $type = ArrayHelper::getValue($settings, 'type', static::INPUT_TEXT);
        $this->prepareField($model, $field, $type, $settings);

        return $field;
    }

    /**
     * @param ActiveField $field
     * @param $type
     * @param array $settings
     * @throws InvalidConfigException
     */
    protected function prepareField(ActiveRecord $model, ActiveField $field, string $type, array $settings)
    {
        $options = ArrayHelper::getValue($settings, 'options', []);
        switch ($type) {
            case static::INPUT_HIDDEN:
            case static::INPUT_TEXT:
            case static::INPUT_TEXTAREA:
            case static::INPUT_PASSWORD:
            case static::INPUT_FILE:
                $field->$type($options);
                break;

            case static::INPUT_DROPDOWN_LIST:
            case static::INPUT_LIST_BOX:
            case static::INPUT_CHECKBOX_LIST:
            case static::INPUT_CHECKBOX:
            case static::INPUT_RADIO_LIST:
                $items = ArrayHelper::getValue($settings, 'items', []);
                $field->$type($items, $options);
                break;

            case static::INPUT_RADIO:
                $enclosedByLabel = ArrayHelper::getValue($settings, 'enclosedByLabel', true);
                $field->$type($options, $enclosedByLabel);
                break;

            case static::INPUT_HTML5:
                $html5type = ArrayHelper::getValue($settings, 'html5type', 'text');
                $field->$type($html5type, $options);
                break;

            case static::INPUT_WIDGET:
                $widgetClass = $this->getWidgetClass($settings);
                $field->$type($widgetClass, $options);
                break;

            case static::INPUT_RAW:
                $field->parts['{input}'] = $this->getValue($settings);
                break;

            case static::INPUT_DYNAMIC_FORM:
                ArrayHelper::remove($settings, 'type');

                /** @var $model ActiveRecord|DynamicFormInterface */
                $this->checkDynamicFormData($model, $field->attribute);

                $relation = $model->getDynamicFormConfig()[$field->attribute]['relation'];

                $relatedModels = $model->relatedModels[$field->attribute];
                $relatedClass = $model->getRelation($relation)->modelClass;
                $minItems = ArrayHelper::getValue($settings, 'min', 0);
                $maxItems = ArrayHelper::getValue($settings, 'max', 999);
                if ($minItems > $maxItems) {
                    throw new InvalidConfigException('Min items amount is more than max');
                }
                $postParams = Yii::$app->getRequest()->post();
                if (empty($relatedModels)) {
                    $relatedModels = $minItems === 0
                        ? [new $relatedClass()]
                        : DynamicForm::generateForms($minItems, $relatedClass, $postParams);
                }
                if (!empty($postParams)) {
                    Model::loadMultiple($relatedModels, $postParams, $relatedModels[0]->formName());
                    Model::validateMultiple($relatedModels);
                }

                $viewData = array_merge(
                    ['form' => $this],
                    ['model' => $model],
                    ['relatedModels' => $relatedModels],
                    $settings
                );
                $field->parts['{input}'] = $this->render(DynamicForm::VIEW_FILE, $viewData);
                break;

            default:
                throw new InvalidConfigException("Invalid input type '{$type}' configured for the attribute.");
        }
    }

    public function checkDynamicFormData(ActiveRecord $model, string $field): void
    {
        if (!$model instanceof DynamicFormInterface) {
            throw new InvalidConfigException('Model must implements DynamicFormInterface');
        }
        if (!isset($model->getDynamicFormConfig()[$field])) {
            throw new InvalidConfigException("Missing `$field` in config");
        }
        $configData = $model->getDynamicFormConfig()[$field];
        if (!isset($configData['relation']) || $model->getRelation($configData['relation']) === null) {
            throw new InvalidConfigException("Missing relation for `$field`");
        }
    }

    /**
     * @param ActiveRecord $model
     * @param array $tabConfig
     *
     * @return string
     * @throws InvalidConfigException
     */
    public function prepareRows(ActiveRecord $model, array $tabConfig): string
    {
        $content = '';
        foreach ($tabConfig as $attribute => $element) {
            $content .= $this->renderField($model, $attribute, $element);
        }

        return $content;
    }

    /**
     * @param array $settings
     * @return mixed
     * @throws InvalidConfigException
     */
    protected function getWidgetClass(array $settings)
    {
        $widgetClass = ArrayHelper::getValue($settings, 'widgetClass');
        if (empty($widgetClass) && !$widgetClass instanceof InputWidget) {
            throw new InvalidConfigException(
                "A valid 'widgetClass' must be setup and extend from '\\yii\\widgets\\InputWidget'."
            );
        }
        return $widgetClass;
    }

    /**
     * @param array $settings
     * @return mixed|string
     */
    protected function getValue(array $settings)
    {
        $value = ArrayHelper::getValue($settings, 'value', '');
        if (is_callable($value)) {
            return call_user_func($value);
        } elseif (!is_string($value)) {
            return '';
        }
        return $value;
    }
}
