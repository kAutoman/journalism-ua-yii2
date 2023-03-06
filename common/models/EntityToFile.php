<?php

namespace common\models;

use metalguardian\fileProcessor\models\File;
use Yii;

/**
 * @inheritdoc
 *
 * @property FpmFile $file
 */
class EntityToFile extends \common\models\base\EntityToFile
{
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFile()
    {
        return $this->hasOne(File::className(), ['id' => 'file_id']);
    }

}
