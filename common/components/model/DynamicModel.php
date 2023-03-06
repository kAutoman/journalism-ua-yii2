<?php

namespace common\components\model;

use yii\base\Model;
use yii\validators\Validator;
use yii\base\InvalidConfigException;

/**
 * Class DynamicModel is an overridden variant of Yii Framework [[\yii\base\DynamicModel]]
 * with labels support and other minor improvements.
 *
 * @var array $rules
 * @var array $labels
 * @var array $dynamicAttributes
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @author Bogdan K. Fedun <delagics@gmail.com>
 */
class DynamicModel extends Model
{
    /** @var array list of dynamic attributes rules. */
    protected $rules = [];
    /** @var array list of specified attribute labels. */
    protected $labels = [];
    /** @var array list of dynamic attributes. */
    protected $dynamicAttributes = [];

    /**
     * Constructors.
     * @param array $attributes the dynamic attributes (name-value pairs, or names).
     * @param array $rules the dynamic attributes validation rules.
     * @param array $labels the dynamic attributes labels (name-value pairs).
     * @param array $config the configuration array to be applied to this object.
     */
    public function __construct(array $attributes = [], array $rules = [], array $labels = [], $config = [])
    {
        foreach ($attributes as $name => $value) {
            if (is_int($name)) {
                $this->dynamicAttributes[$value] = null;
                $this->labels[$value] = obtain($value, $labels);
            } else {
                $this->dynamicAttributes[$name] = $value;
                $this->labels[$name] = obtain($name, $labels);
            }
        }

        $this->rules = $rules;

        parent::__construct($config);
    }

    public function attributes()
    {
        return array_keys($this->dynamicAttributes);
    }

    public function getAttributeLabel($name)
    {
        return $this->labels[$name] ?? $this->generateAttributeLabel($name);
    }

    /**
     * Defines an attribute.
     * @param string $name the attribute name
     * @param mixed $value the attribute value
     */
    public function defineAttribute($name, $value = null)
    {
        $this->dynamicAttributes[$name] = $value;
    }

    /**
     * Undefines an attribute.
     * @param string $name the attribute name
     */
    public function undefineAttribute($name)
    {
        unset($this->dynamicAttributes[$name]);
    }

    public function validate($attributeNames = null, $clearErrors = true)
    {
        if (!empty($this->rules)) {
            $validators = $this->getValidators();
            foreach ($this->rules as $rule) {
                if ($rule instanceof Validator) {
                    $validators->append($rule);
                } elseif (is_array($rule) && isset($rule[0], $rule[1])) { // attributes, validator type
                    $validator = Validator::createValidator($rule[1], $this, (array)$rule[0], array_slice($rule, 2));
                    $validators->append($validator);
                } else {
                    throw new InvalidConfigException('Invalid validation rule: a rule must specify both attribute names and validator type.');
                }
            }
        }

        return parent::validate($attributeNames, $clearErrors);
    }

    /**
     * Adds a validation rule to this model.
     * You can also directly manipulate [[validators]] to add or remove validation rules.
     * This method provides a shortcut.
     * @param string|array $attributes the attribute(s) to be validated by the rule
     * @param mixed $validator the validator for the rule.This can be a built-in validator name,
     * a method name of the model class, an anonymous function, or a validator class name.
     * @param array $options the options (name-value pairs) to be applied to the validator
     * @return $this the model itself
     */
    public function addRule($attributes, $validator, $options = [])
    {
        $validators = $this->getValidators();
        $validators->append(Validator::createValidator($validator, $this, (array)$attributes, $options));

        return $this;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->dynamicAttributes)) {
            return $this->dynamicAttributes[$name];
        }

        return parent::__get($name);
    }

    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->dynamicAttributes)) {
            $this->dynamicAttributes[$name] = $value;
        } else {
            parent::__set($name, $value);
        }
    }

    public function __isset($name)
    {
        if (array_key_exists($name, $this->dynamicAttributes)) {
            return isset($this->dynamicAttributes[$name]);
        }

        return parent::__isset($name);
    }

    public function __unset($name)
    {
        if (array_key_exists($name, $this->dynamicAttributes)) {
            unset($this->dynamicAttributes[$name]);
        } else {
            parent::__unset($name);
        }
    }
}
