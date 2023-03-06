<?php

namespace common\modules\config\application\validators;

use common\helpers\LanguageHelper;
use Yii;
use yii\validators\Validator;
use common\models\FpmFile;
use common\modules\config\domain\repositories\StorageRepository;
use common\modules\config\infrastructure\repositories\IStorageRepository;

/**
 * Class FileRequiredValidator
 * Validation class for configurator.
 * Check if file exist on single and multiple uploads.
 *
 * @package common\modules\config\application\validators
 * @author Andrew P. Kontseba <andjey.skinwalker@gmail.com>
 */
class FileRequiredValidator extends Validator
{
    /**  @var bool Skip check on empty filed value. */
    public $skipOnEmpty = true;
    /** @var string Message for error. */
    public $message;

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
        $key = str_replace('_', '.', $attribute);
        /** @var StorageRepository $repository */
        $repository = container()->get(IStorageRepository::class);
        $fileId = $repository->find($key, LanguageHelper::getEditLanguage())->getValue();
        if ($fileId && FpmFile::find()->where(['id' => explode(',', $fileId)])->exists()) {
            return;
        }
        $this->addError($model, $attribute, $this->message);
    }
}
