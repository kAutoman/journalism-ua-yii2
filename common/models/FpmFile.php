<?php

namespace common\models;

use Yii;

/**
 * @inheritdoc
 *
 * @property EntityToFile[] $entityToFiles
 * @property FileMetaData $meta
 */
class FpmFile extends \common\models\base\FpmFile
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return \yii\helpers\ArrayHelper::merge(parent::behaviors(), [
            'timestamp' => [
                'class' => \yii\behaviors\TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEntityToFiles()
    {
        return $this->hasMany(EntityToFile::class, ['file_id' => 'id']);
    }

    public function getMeta()
    {
        return $this->hasOne(FileMetaData::class, ['file_id' => 'id'])
            ->andOnCondition(['language' => app()->language]);
    }

}
