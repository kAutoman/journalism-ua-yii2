<?php

namespace backend\modules\imagesUpload\models;

use common\helpers\LanguageHelper;
use common\helpers\UrlHelper;
use metalguardian\formBuilder\ActiveFormBuilder;
use Yii;

/**
 * Class FileMetaData
 * @package backend\modules\imagesUpload\models
 * @author Andrew P. Kontseba <andjey.skinwalker@gmail.com>
 *
 * @property array $formConfig
 */
class FileMetaData extends \common\models\FileMetaData
{
    /**
     * @param int $fileId
     *
     * @return \backend\modules\imagesUpload\models\FileMetaData
     */
    public static function getModelByFileId(int $fileId): FileMetaData
    {
        $model = self::find()
            ->andWhere([
                'file_id' => $fileId,
                'language' => LanguageHelper::getEditLanguage()
            ])
            ->one();

        if ($model === null) {
            $model = new FileMetaData();
            $model->file_id = $fileId;
        }

        return $model;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['alt'], 'string', 'max' => 255],
            [['title'], 'string', 'max' => 255],
            [['language'], 'string', 'max' => 16],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'alt'     => Yii::t('back/fileMetaData', 'Alt'),
        ];
    }

    /**
     * @return array
     */
    public function getFormConfig()
    {
        return [
            'language' => [
                'type'    => ActiveFormBuilder::INPUT_HIDDEN,
                'label' => false,
                'options' => ['value' => LanguageHelper::getEditLanguage()]
            ],
            'alt' => [
                'type'    => ActiveFormBuilder::INPUT_TEXT,
            ],
            'title' => [
                'type'    => ActiveFormBuilder::INPUT_TEXT,
            ],
        ];
    }

    public function getMetaDataSaveUrl()
    {
        return UrlHelper::createUrl('/imagesUpload/meta-data/save-form', ['id' => $this->file_id]);
    }
}
