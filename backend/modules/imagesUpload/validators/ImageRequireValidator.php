<?php

namespace backend\modules\imagesUpload\validators;

use common\models\EntityToFile;
use yii\validators\Validator;

/**
 * Class ImageRequireValidator
 * Fixed from vadymsemenykv\imageRequireValidator
 * @author Andrew Kontseba <andjey.skinwalker@gmail.com>
 * @package backend\modules\imagesUpload\validators
 *
 * @deprecated
 */
class ImageRequireValidator extends Validator
{
    public $imageRelation = 'image';
    public $imageAttribute = 'main-image-attribute';
    public $errorMessage = 'Image cannot be blank.';
    public $errorNumMinMessage = 'Image cannot be blank.';
    public $errorNumMaxMessage = 'Image cannot be blank.';

    public $validateNum = false;
    public $minNumOfImages = null;
    public $maxNumOfImages = null;

    /**
     * @param \yii\base\Model $model
     * @param string $attribute
     */
    public function validateAttribute($model, $attribute)
    {
        $imageSavedBySign = EntityToFile::find()
            ->where([
                'temp_sign' => $model->sign,
                'attribute' => $this->imageAttribute
            ])
            ->exists();
        $imageRelation = $model->{$this->imageRelation};
        if (!$imageRelation && !$imageSavedBySign) {
            $this->addError($model, $attribute, $this->errorMessage);
        }
        if ($this->validateNum) {
            $this->validateByNum($model, $attribute, $imageRelation, $imageSavedBySign);
        }

    }

    /**
     * @param $model
     * @param $attribute
     * @param $imageRelation
     * @param $imageSavedBySign
     */
    private function validateByNum($model, $attribute, $imageRelation, $imageSavedBySign)
    {
        if ($this->minNumOfImages !== null && $this->validateForMinValue($imageRelation, $imageSavedBySign)) {
            $this->addError($model, $attribute, $this->errorNumMinMessage);
        }
        if ($this->maxNumOfImages !== null && $this->validateForMaxValue($imageRelation, $imageSavedBySign)) {
            $this->addError($model, $attribute, $this->errorNumMaxMessage);
        }
    }

    /**
     * @param $imageRelation
     * @param $imageSavedBySign
     * @return bool
     */
    private function validateForMinValue($imageRelation, $imageSavedBySign)
    {
        return !((count($imageRelation) >= $this->minNumOfImages) || (count($imageSavedBySign) >= $this->minNumOfImages));
    }

    /**
     * @param $imageRelation
     * @param $imageSavedBySign
     * @return bool
     */
    private function validateForMaxValue($imageRelation, $imageSavedBySign)
    {
        return (
            !(count($imageRelation) <= $this->maxNumOfImages) ||
            !(count($imageSavedBySign) <= $this->maxNumOfImages) ||
            !((count($imageRelation) + count($imageSavedBySign)) <= $this->maxNumOfImages)
        );
    }
}
