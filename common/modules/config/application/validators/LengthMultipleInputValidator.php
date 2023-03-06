<?php

namespace common\modules\config\application\validators;

use common\helpers\LanguageHelper;
use common\modules\config\infrastructure\repositories\IStorageRepository;
use yii\validators\Validator;

class LengthMultipleInputValidator extends Validator
{
    /**  @var bool Skip check on empty filed value. */
    public $skipOnEmpty = false;
    /** @var string Message for error. */
    public $message;

    public $field;
    public $length;

    public function init()
    {
        if ($this->message === null) {
            $this->message = bt('Field "{attribute}" can`t be empty', 'error');
        }
    }

    /**
     * @param \yii\base\Model $model
     * @param string $attribute
     */
    public function validateAttribute($model, $attribute)
    {
        echo '<pre>';
        var_dump($model);
        echo '</pre>';
        die();
//        $key = str_replace('_', '.', $attribute);
//        /** @var StorageRepository $repository */
//        $repository = container()->get(IStorageRepository::class);
//        $value = $repository->find($key, LanguageHelper::getEditLanguage())->getValue();
//        $value = json_decode($value, true);
//        foreach ($value as $one) {
//
//        }
//        print_r($value);
       $this->addError($model, $attribute, $this->message);
    }
}
