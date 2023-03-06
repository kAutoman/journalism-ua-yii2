<?php


namespace common\modules\config\application\validators;


use Yii;
use yii\validators\Validator;

class MultiSelectMaxValidator  extends Validator
{

    /**@var int  $max*/
    public $max;
    public $min;



    /**
     * @param \yii\base\Model $model
     * @param string $attribute
     */
    public function validateAttribute($model, $attribute)
    {
        if(!is_array($model->$attribute )){
            $this->addError($model, $attribute, Yii::t('back/base','This field is required'));
            return;
        }

        if($this->max &&  count($model->$attribute) > $this->max){
            $this->addError($model, $attribute, Yii::t('back/base','Only {max}  items you can select', ['max'=>$this->max]));
        }
        if($this->min && count($model->$attribute) < $this->min){

            $this->addError($model, $attribute, Yii::t('back/base','Minimum {min}  items you must select', ['min'=>$this->min]));
        }
    }



}
