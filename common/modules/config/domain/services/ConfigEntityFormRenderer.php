<?php

namespace common\modules\config\domain\services;

use yii\bootstrap\Tabs;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveFormAsset;
use yii\validators\ValidationAsset;
use common\modules\config\infrastructure\entities\IConfigEntity;
use common\modules\config\infrastructure\services\IConfigEntityFormRenderer;
use common\modules\config\infrastructure\collections\IConfigEntityCollection;

/**
 * Class ConfigEntityFormRenderer
 *
 * @author Bogdan K. Fedun <delagics@gmail.com>
 */
class ConfigEntityFormRenderer implements IConfigEntityFormRenderer
{
    /**
     * @var string|array form action.
     */
    public $action;
    /**
     * @var string from method.
     */
    public $method = 'post';
    /**
     * @var array form options.
     */
    public $options = ['id' => 'config-entity-form', 'enctype' => 'multipart/form-data', 'csrf' => false, 'class' => 'block'];
    /**
     * @var bool whether to initiate validation when input changes its value.
     */
    public $validateOnChange = true;
    /**
     * @var bool whether to initiate validation on when input loses its focus.
     */
    public $validateOnBlur = true;
    /**
     * @var bool whether to initiate validation on typing an input value.
     */
    public $validateOnType = false;
    /**
     * @var bool validation process delay.
     */
    public $validationDelay = 500;
    /**
     * @var IConfigEntityCollection|null
     */
    private $entities;
    /**
     * @var string validation URL.
     */
    private $validationUrl;

    public function __construct(IConfigEntityCollection $collection = null)
    {
        $this->entities = $collection;
    }

    public function getEntities(): IConfigEntityCollection
    {
        return $this->entities;
    }

    public function setEntities(IConfigEntityCollection $entities): void
    {
        $this->entities = $entities;
    }

    public function renderForm(): string
    {
        $items = [];
        $this->getEntities()->each(function (IConfigEntity $entity, $i) use (&$output, &$items) {
            ArrayHelper::setValue($items, [$entity->getField()->getTab(), $i], $entity->getField()->render());
        });
        $tabs = [];

        foreach ($items as $tabName => $tabContent) {
            $tabs[] = ['label' => $tabName, 'content' => implode("\n", $tabContent)];
        }
        $output = Html::beginForm($this->action, $this->method, $this->options);
        $output .= Html::beginTag('div', ['class' => 'nav-tabs-custom tab-primary']);
        $output .= Tabs::widget(['items' => $tabs, 'navType' => 'nav-tabs nav-tabs-alt']);
        $output .= Html::endTag('div');
        $output .= $this->renderSubmit();
        $output .= Html::endForm();

        $this->registerClientScript();

        return $output;
    }

    public function setValidationUrl(array $value): void
    {
        $this->validationUrl = Url::to($value);
    }

    public function getValidationUrl(): string
    {
        return $this->validationUrl;
    }

    public function renderSubmit(): string
    {
        $output = '<div class="form-group">';
        $output .= Html::submitButton(bt('Save', 'config'), ['class' => 'btn btn-flat btn-success black']);
        $output .= '</div>';

        return $output;
    }

    protected function registerClientScript()
    {
        $formId = obtain('id', $this->options, 'config-entity-form');
        $view = app()->getView();
        ActiveFormAsset::register($view);
        ValidationAsset::register($view);
        $options = [
            'validationUrl' => $this->getValidationUrl(),
            'validateOnBlur' => $this->validateOnBlur,
            'validateOnType' => $this->validateOnType,
            'validationDelay' => $this->validationDelay,
            'validateOnChange' => $this->validateOnChange,
        ];
        $attributes = [];
        $entities = $this->getEntities();
        /** @var IConfigEntity $entity */
        foreach ($entities as $key => $entity) {
            $inputID = $entity->getField()->getInputId();
            $attributes[$key]['id'] = $inputID;
            $attributes[$key]['name'] = $entity->getField()->getName();
            $attributes[$key]['container'] = ".field-$inputID";
            $attributes[$key]['input'] = "#$inputID";
            $attributes[$key]['enableAjaxValidation'] = true;
            $attributes[$key]['updateAriaInvalid'] = false;
            // only get the options that are different from the default ones (set in yii.activeForm.js)
            $attributes[$key] = array_diff_assoc($attributes[$key], [
                'error' => '.help-block',
                'encodeError' => false,
                'updateAriaInvalid' => false,
            ]);
        }

        $options = Json::htmlEncode($options);
        $attributes = Json::htmlEncode(array_values($attributes));

        $view->registerJs("jQuery('#$formId').yiiActiveForm($attributes, $options);");

        return $options;
    }

    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }
    }

    public function __set($name, $value)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter($value);

            return;
        }
    }
}
