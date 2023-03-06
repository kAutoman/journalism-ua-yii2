<?php


namespace common\modules\config\application\validators;


use yii\helpers\Json;
use yii\validators\RequiredValidator;
use yii\validators\Validator;

class RequiredMultipleInputValidator extends Validator
{

    /**@var array $fileds */
    public $fields;


    /**
     * @param \yii\base\Model $model
     * @param string $attribute
     */
    public function validateAttribute($model, $attribute)
    {
        foreach ($model->$attribute as $key => $row) {
            $validator = new RequiredValidator();
            foreach ($this->fields as $field) {
                $error = false;
                $validator->validate($row[$field], $error);
                $keyAttr = $attribute . '-' . $key . '-' . $field;
                if (!!$error) {
                    $this->addError($model, $keyAttr, $error, ['value' => 'Title']);
                }
            }
        }

    }


}
