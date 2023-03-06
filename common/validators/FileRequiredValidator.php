<?php

namespace common\validators;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\db\Query;
use yii\validators\Validator;
use common\models\EntityToFile;
use common\modules\builder\models\BuilderModel;

/**
 * Class FileRequireValidator
 *
 *  Example
 *  [
 *      ['file_attribute'],
 *      'file-required',
 *      'max' => 2 // set max files
 *      'min' => 4 //set min files,
 *      'saveAttribute' => 'saveAttribute' //attribute to be saved
 *  ]
 *
 * @package common\validators
 */
class FileRequiredValidator extends Validator
{
    /** @var  string model save attribute */
    public $saveAttribute;

    public $min;
    public $max;

    public $requiredMessage;
    public $minMessage;
    public $maxMessage;

    public function init()
    {
        $this->min = ($this->min > 0) ? $this->min : null;
        $this->max = ($this->max > 0) ? $this->max : null;

        if ($this->min !== null && $this->minMessage === null) {
            $this->minMessage = Yii::t('validation', '{attribute} should contain at least {min, number} {min, plural, one{file} other{files}}.');
        }

        if ($this->max !== null && $this->maxMessage === null) {
            $this->maxMessage = Yii::t('validation', '{attribute} should contain at most {max, number} {max, plural, one{file} other{files}}.');
        }

        $this->requiredMessage = Yii::t('validation', '{attribute} cannot be blank');

        parent::init();
    }

    /**
     * @param Model $model
     * @param string $attribute
     */
    public function validateAttribute($model, $attribute)
    {
        $count = $this->getFilesCount($model);

        if (! $count) {
            $this->addError($model, $attribute, $this->requiredMessage);
        }

        if ($this->max && $count > $this->max) {
            $this->addError($model, $attribute, $this->maxMessage, ['max' => $this->max]);
        }

        if ($this->min && $count < $this->min) {
            $this->addError($model, $attribute, $this->maxMessage, ['min' => $this->min]);
        }
    }

    /**
     * @param $model
     * @return int|string
     */
    protected function getFilesCount($model)
    {
        $query = (new Query())->select('id')
            ->from(EntityToFile::tableName())
            ->where(['AND',
                ['entity_model_name' => $model->formName()],
                ['attribute' => $this->saveAttribute]]);

        if ($model instanceof BuilderModel) {
            $query->andWhere(['temp_sign' => $model->target_sign]);
        } else {
            $model->id
                ? $query->andWhere(['entity_model_id' => $model->id])
                : $query->andWhere(['temp_sign' => $model->sign]);
        }

        return $query->count();
    }
}
