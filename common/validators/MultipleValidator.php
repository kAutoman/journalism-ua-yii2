<?php

namespace common\validators;

use yii\base\InvalidConfigException;
use yii\validators\Validator;

/**
 * Class MultipleValidator
 * Validates the attribute used for the multiple column widget.
 * Checks each column with the usual yii rules
 *
 * Example
 *
 *  [['modelAttribute'], 'multiple', 'rules' => [
 *      [['columnAttribute1', 'columnAttribute2'], 'required'],
 *      ['columnAttribute1', 'string', 'max' => 255],
 *      ['columnAttribute2', 'string', 'max' => 500],
 *  ], 'onlyGlobalError' => true], //if needed global error
 * @package common\validators
 */
class MultipleValidator extends Validator
{
    /**
     * @var array
     */
    public $rules = [];

    /**
     * Outputs one global error for a model attribute
     * @var bool
     */
    public $onlyGlobalError = false;

    /**
     * @param \yii\base\Model $model
     * @param string $attribute
     * @throws InvalidConfigException
     */
    public function validateAttribute($model, $attribute)
    {
        foreach ($this->rules as $rules) {
            $validator = $this->createOneValidator($rules, $model);
            $ruleAttributes = $this->getAttributesFromRule($rules);
            foreach ($model->$attribute as $key => $item) {
                foreach ($ruleAttributes as $ruleAttribute) {
                    $error = '';
                    $validator->validate($item[$ruleAttribute], $error);

                    if (!empty($error)) {
                        if ($this->onlyGlobalError) {
                            $this->addError($model, $attribute, $error);
                        } else {
                            $errorKey = $attribute . '[' . $key . '][' . $ruleAttribute . ']';
                            $errors[$errorKey] = $error;
                            $model->addErrors($errors);
                        }
                    }
                }
            }
        }

    }

    /**
     * @param \Closure|string $rule
     * @param \yii\base\Model $model
     * @return \Closure|string|Validator
     * @throws InvalidConfigException
     */
    private function createOneValidator($rule, $model)
    {
        if ($rule instanceof Validator) {
            return $rule;
        } elseif (is_array($rule) && isset($rule[0], $rule[1])) { // attributes, validator type
            return Validator::createValidator($rule[1], $model, $this->getAttributesFromRule($rule), array_slice($rule, 2));
        } else {
            throw new InvalidConfigException('Invalid validation rule: a rule must specify both attribute names and validator type.');
        }
    }

    /**
     * @param $rule
     * @return array
     */
    private function getAttributesFromRule($rule): array
    {
        return (array) $rule[0];
    }
}