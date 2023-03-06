<?php

namespace common\models;

use common\components\model\ActiveRecord;

/**
 * Class FileMetaData
 *
 * @property int $file_id
 * @property string $language
 * @property string $alt
 * @property string $title
 *
 * @package common\models
 * @author Andrew P. Kontseba <andjey.skinwalker@gmail.com>
 */
class FileMetaData extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%fpm_meta_data}}';
    }

    public static function primaryKey()
    {
        return ['file_id', 'language'];
    }

    public static function getMetaByFileId(int $fileId)
    {
        return self::find()->where(['file_id' => $fileId, 'language' => app()->language])->one();
    }
}
