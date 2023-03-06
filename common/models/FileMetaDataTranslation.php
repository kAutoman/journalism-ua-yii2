<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%file_meta_data_translation}}".
 *
 * @property integer $model_id
 * @property string  $language
 * @property string  $alt
 */
class FileMetaDataTranslation extends \common\components\model\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%file_meta_data_translation}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'alt' => Yii::t('back/fileMetaData', 'Alt') . ' [' . $this->language . ']',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['alt'], 'string', 'max' => 255],
        ];
    }
}
