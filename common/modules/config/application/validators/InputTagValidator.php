<?php


namespace common\modules\config\application\validators;


use yii\validators\RequiredValidator;
use yii\validators\StringValidator;
use yii\validators\Validator;

/**
 * Class InputTagValidator
 *
 * @package common\modules\config\application\validators
 * @deprecated
 */
class InputTagValidator extends Validator
{
    public $max;

    public function validateAttribute($model, $attribute)
    {
        $requiredValidator = new RequiredValidator();
        $error = false;
        $requiredValidator->validate($model->$attribute['label'], $error);
        $stringValidator = new StringValidator(['max'=>$this->max]);
        $stringValidator->validate($model->$attribute['label'], $error);
        if($error){
            $this->addError($model, $attribute, $error);
        }
    }
}
