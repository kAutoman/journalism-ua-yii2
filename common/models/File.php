<?php


namespace common\models;


use yii\helpers\ArrayHelper;

class File extends \metalguardian\fileProcessor\models\File
{
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['position'], 'integer']
        ]);
    }
}
