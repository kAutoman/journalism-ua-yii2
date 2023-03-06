<?php

namespace common\modules\fileManager\models;

use common\helpers\UrlHelper;
use Yii;
use common\components\model\ActiveRecord;

/**
 * This is the model class for table "file".
 *
 * @property int $id
 * @property string $path File path
 * @property string $base_url Base URL
 * @property string $type Type
 * @property int $size File size
 * @property string $name Name
 * @property int $order Position
 * @property string $entity_model_name Entity model name
 * @property int $entity_model_id Entity model ID
 * @property string $entity_model_attribute Entity model attribute
 */
class File extends ActiveRecord
{
    public $red = 'redd';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'file';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
//            [['path', 'base_url', 'type', 'entity_model_name', 'entity_model_attribute'], 'required'],
            [['path'], 'string'],
            [['size', 'order', 'entity_model_id'], 'integer'],
            [['base_url', 'type', 'name', 'entity_model_name', 'entity_model_attribute'], 'string', 'max' => 255],
            [['img_alt', 'img_title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('back/file_manager', 'ID'),
            'path' => Yii::t('back/file_manager', 'File path'),
            'img_alt' => Yii::t('back/file_manager', 'Image alt text'),
            'img_title' => Yii::t('back/file_manager', 'Image title text'),
            'base_url' => Yii::t('back/file_manager', 'Base URL'),
            'type' => Yii::t('back/file_manager', 'Type'),
            'size' => Yii::t('back/file_manager', 'File size'),
            'name' => Yii::t('back/file_manager', 'Name'),
            'order' => Yii::t('back/file_manager', 'Order'),
            'entity_model_name' => Yii::t('back/file_manager', 'Entity model name'),
            'entity_model_id' => Yii::t('back/file_manager', 'Entity model ID'),
            'entity_model_attribute' => Yii::t('back/file_manager', 'Entity model attribute'),
        ];
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public static function getDeleteUrl($params = [])
    {
        return UrlHelper::createUrl('/file-manager/file/delete', $params);
    }
}
