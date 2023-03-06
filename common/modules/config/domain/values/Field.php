<?php

namespace common\modules\config\domain\values;

use ArrayObject;
use yii\helpers\Html;
use yii\validators\Validator;
use yii\base\InvalidConfigException;
use common\modules\config\infrastructure\values\IField;
use common\modules\config\application\validators\FileRequiredValidator;

/**
 * Class Field - base class for all fields.
 *
 * @author Bogdan K. Fedun <delagics@gmail.com>
 */
abstract class Field implements IField
{
    private $inputId;
    private $key;
    private $name;
    private $type;
    private $label;
    private $description;
    private $default;
    private $value;
    private $rules;
    private $options;
    private $display;
    private $autoload;
    private $hint;
    private $aggregate;
    private $tab;

    public function __construct(
        string $key,
        string $type,
        string $label,
        string $description,
        $default = null,
        $value = null,
        array $rules = [],
        array $options = [],
        bool $display = false,
        bool $autoload = false,
        $hint = false,
        bool $aggregate = false,
        ?string $tab = null
    )
    {
        $this->key = $key;
        $this->type = $type;
        $this->label = $label;
        $this->description = $description;
        $this->default = $default;
        $this->value = $value;
        $this->rules = $rules;
        $this->options = $options;
        $this->display = $display;
        $this->autoload = $autoload;
        $this->hint = $hint;
        $this->aggregate = $aggregate;
        $this->tab = $tab;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getName(): string
    {
        if ($this->name === null) {
            $this->name = str_replace('.', '_', $this->key);
        }

        return $this->name;
    }

    public function getInputId(): string
    {
        if ($this->inputId === null) {
            $this->inputId = $this->getName();
        }

        return $this->inputId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getLabel(): string
    {
        return bt($this->label, 'config');
    }

    public function getHint()
    {
        return $this->hint ?? false;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getDefault()
    {
        return $this->default;
    }

    public function getValue()
    {
        return $this->value === null ? $this->default : $this->value;
    }

    public function setValue($value): void
    {
        $this->value = $value;
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getIsDisplayable(): bool
    {
        return $this->display;
    }

    public function getIsAutoloadable(): bool
    {
        return $this->autoload;
    }

    public function getIsAggregated(): bool
    {
        return $this->aggregate;
    }

    public function validate(): bool
    {
        return true;
    }

    public function getTab()
    {
        return $this->tab;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'type' => $this->getType(),
            'label' => $this->getLabel(),
            'description' => $this->getDescription(),
            'default' => $this->getDefault(),
            'value' => $this->getValue(),
            'rules' => $this->getRules(),
            'options' => $this->getOptions(),
            'display' => $this->getIsDisplayable(),
            'autoload' => $this->getIsAutoloadable(),
        ];
    }

    public function createValidators()
    {
        $validators = new ArrayObject();
        $rules = $this->getRules();
        array_unshift($rules, ['safe']);
        foreach ($rules as $rule) {
            if ($rule instanceof Validator) {
                $validators->append($rule);
            } elseif (is_array($rule) && isset($rule[0])) { // attributes, validator type
                $validator = Validator::createValidator($rule[0], $this, ['value'], array_slice($rule, 1));
                $validators->append($validator);
            } else {
                throw new InvalidConfigException('Invalid validation rule: a rule must specify validator type.');
            }
        }

        return $validators;
    }

    public function getDefaultOptions()
    {
        return [
            'id' => $this->getInputId(),
            'class' => ['form-control'],
            'placeholder' => $this->getDescription(),
        ];
    }

    public function preparedOptions()
    {
        return merge($this->getDefaultOptions(), $this->getOptions());
    }

    public function beforeRender()
    {
        $options = ['class' => ['form-group', 'field-' . $this->getInputId()]];
        if ($this->isRequired()) {
            Html::addCssClass($options, obtain('requiredCssClass', $this->getOptions(), 'required'));
        }

        return Html::beginTag('div', $options);
    }

    /**
     * @return string
     */
    public function afterRender() : string
    {
        return '<div class="help-block"></div></div>';
    }

    /**
     * @return bool
     */
    private function isRequired(): bool
    {
        foreach ($this->rules as $rule) {
            $name = obtain(0, $rule);
            $when = obtain('when', $rule);
            if (($name === 'required' || $name == FileRequiredValidator::class) && $when === null) {
                return true;
            }
        }
        return false;
    }
}
