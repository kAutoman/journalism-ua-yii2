<?php
/**
 * Author: metal
 * Email: metal
 */

namespace backend\components;

use common\components\interfaces\TranslateableInterface;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * Class LanguageDetailView
 *
 * @package backend\components
 */
class LanguageDetailView extends DetailView
{
    public function init()
    {
//        /** @var \common\components\model\ActiveRecord $model */
//        $model = $this->model;
//
//        if ($model->isTranslatable()) {
//            var_dump($this->attributes);die;
//            foreach ($this->attributes as $key => $attribute) {
//                if ($model->hasAttribute($attribute)) {
//                    $langAttributes = [];
//                    foreach ($model->translations as $lang => $model) {
//                        $langAttributes[] = [
//                            'label' => $attribute . " [{$lang}]",
//                            'value' => $model->$attribute
//                        ];
//                    }
//
//                    unset($this->attributes[$key]);
//                    $this->attributes = ArrayHelper::merge($this->attributes, $langAttributes);
//                }
//
//            }
//        }

        return parent::init();
    }
}
