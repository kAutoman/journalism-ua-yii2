<?php

namespace backend\modules\imagesUpload\controllers;

use common\components\model\ActiveRecord;
use common\helpers\MediaHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use metalguardian\fileProcessor\models\File;
use metalguardian\fileProcessor\helpers\FPM;
use metalguardian\fileProcessor\components\Image;
use common\models\EntityToFile;
use backend\components\BackendController;
use backend\modules\imagesUpload\models\ImagesUploadModel;

/**
 * DefaultController implements the CRUD actions for Configuration model.
 */
class DefaultController extends BackendController
{
    /**
     * @inheritdoc
     */
    public function getModelClass(): string
    {
        return ImagesUploadModel::class;
    }

    /**
     * @return null|string
     */
    public function getLangModelClass(): ?string
    {
        return null;
    }

    /**
     * @return string
     * @throws \Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUploadImage()
    {
        $returnData = [];

        $className = \Yii::$app->request->get('model_name');
        $attribute = \Yii::$app->request->get('attribute');
        $maxFileCount = \Yii::$app->request->get('max_file_count') ?? 1;
        $entity_attribute = \Yii::$app->request->get('entity_attribute') ?? null;
        if ($className && $attribute) {
            /** @var ActiveRecord $model */
            $model = new $className;
            $modelName = $model->formName();

            $files = UploadedFile::getInstances($model, $attribute);
            foreach ($files as $file) {
                $originalName = $file->baseName . '.' . $file->extension;

                $fileId = FPM::transfer()->saveUploadedFile($file);
                if ($fileId) {

                    $existModelId = \Yii::$app->request->post('id');
                    $tempSign = \Yii::$app->request->post('sign');

                    // TODO: Need normal fix
                    if($tempSign == 'null'){
                        $tempSign=null;
                    }

                    if (!!$maxFileCount) {

                        $queryCount = EntityToFile::find()->andWhere([
                            'entity_model_name' => $modelName,
                            'attribute' => $entity_attribute
                        ]);
                        $queryCount = !!$existModelId ? $queryCount->andWhere(['entity_model_id' => $existModelId])
                            : $queryCount->andWhere(['temp_sign' => $tempSign]);
                        $count = $queryCount->count();

                        if (!!(int)$tempSign) {
                            $queryCountLoaded = EntityToFile::find()->andWhere([
                                'entity_model_name' => $modelName,
                                'attribute' => $entity_attribute,
                                'entity_model_id' => 0,
                                'temp_sign' => $tempSign
                            ])->count();

                            $count += $queryCountLoaded;
                        }

                        if ($count > $maxFileCount) {
                            $returnData['error'][] = 'Файл не загружен. Максимальное число файлов - ' . $maxFileCount;
                            return Json::encode($returnData);
                        }
                    }
                    $savedImage = EntityToFile::add(
                        $modelName,
                        $existModelId,
                        $fileId,
                        $tempSign,
                        \Yii::$app->request->get('entity_attribute')
                    );

                    if (!$savedImage) {
                        $returnData['error'][] = 'Не получилось связать файл ' . $originalName . ' с моделью';
                    } else {
                        $returnData = [
                            'deleteUrl' => ImagesUploadModel::deleteImageUrl(['id' => $savedImage->id]),
                            'cropUrl' => ImagesUploadModel::getCropUrl(['id' => $savedImage->id]),
                            'id' => $savedImage->id,
                            'imgId' => $savedImage->file_id,
                            'url' => FPM::originalSrc($savedImage->file_id)
                        ];
                    }
                    MediaHelper::optimize($fileId);
                } else {
                    $returnData['error'][] = 'Не получилось сохранить файл ' . $originalName;
                }
            }
        }

        return Json::encode($returnData);
    }

    /**
     * @return string
     * @throws \Throwable
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeleteImage()
    {
        $returnData = [];

        $id = \Yii::$app->request->get('id');

        if ($id) {
            $imageEntity = EntityToFile::find()->where('id = :id', [':id' => (int)$id])->one();
            if ($imageEntity) {
                MediaHelper::delete($imageEntity->file_id);
                if (!$imageEntity->delete()) {
                    $returnData[] = ['error' => 'Не удалось удалить файл'];
                }
            } else {
                $returnData[] = ['error' => 'Информация о изображении не найдена'];
            }
        }

        return Json::encode($returnData);
    }

    /**
     * @throws \yii\db\Exception
     */
    public function actionSortImages()
    {
        $sortOrder = \Yii::$app->request->post('sort');

        if ($sortOrder) {
            $sortOrder = explode(',', $sortOrder);
            $i = count($sortOrder);
            foreach ($sortOrder as $fileId) {
                \Yii::$app->db->createCommand()->update(
                    EntityToFile::tableName(),
                    [
                        'position' => $i
                    ],
                    'id = :id',
                    [':id' => (int)$fileId]
                )->execute();

                $i--;
            }
        }

        echo Json::encode([]);
    }

    /**
     * @return string
     */
    public function actionCrop()
    {
        $fileId = \Yii::$app->request->get('id');

        if (!$fileId) {
            return 'Кроп доступен только после загрузки изображения';
        }

        $imageEntity = EntityToFile::find()->where('id = :id', [':id' => (int)$fileId])->one();

        return $this->renderAjax('_crop_image', [
            'id' => $imageEntity ? $imageEntity->file_id : null,
        ]);
    }

    /**
     * @return string
     * @throws \Exception
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSaveCroppedImage()
    {
        $data = \Yii::$app->request->post('data');
        $data = $data ? Json::decode($data) : null;

        if ($data) {
            $fileId = $data['fileId'];

            $imageEntity = EntityToFile::find()->where('file_id = :id', [':id' => (int)$fileId])->one();

            if ($imageEntity) {
                //Find original img path
                $directory = FPM::getOriginalDirectory($imageEntity->file_id);
                FileHelper::createDirectory($directory, 0777, true);
                $fileName =
                    $directory
                    . DIRECTORY_SEPARATOR
                    . FPM::getOriginalFileName(
                        $imageEntity->file_id,
                        $imageEntity->file->base_name,
                        $imageEntity->file->extension
                    );
                //Delete cached image
                FPM::cache()->delete($imageEntity->file_id);
                //Delete thumbs
                $this->clearImageThumbs($imageEntity->file);

                Image::crop($fileName, $data['width'], $data['height'], $data['startX'], $data['startY'])
                    ->save($fileName, ['animated' => $imageEntity->file->extension === 'gif']);

                return Json::encode(
                    [
                        'replaces' => [
                            [
                                'what' => '#preview-image-' . $imageEntity->file_id,
                                'data' => Html::img(
                                    FPM::originalSrc($imageEntity->file_id) . '?' . time(),
                                    [
                                        'class' => 'file-preview-image',
                                        'id' => 'preview-image-' . $imageEntity->file_id
                                    ]
                                )
                            ]
                        ],
                        'js' => Html::script('hideModal(".modal")')
                    ]
                );
            }
        }

        return '';
    }

    /**
     * Delete all previously generated image thumbs
     *
     * @param File $model
     *
     * @throws \yii\base\InvalidConfigException
     */
    protected function clearImageThumbs(File $model)
    {
        $fp = \Yii::$app->getModule('fileProcessor');

        if ($fp) {
            $imageSections = $fp->imageSections;

            foreach ($imageSections as $moduleName => $config) {

                foreach ($config as $size => $data) {
                    $thumbnailFile = FPM::getThumbnailDirectory($model->id, $moduleName, $size) . DIRECTORY_SEPARATOR .
                        FPM::getThumbnailFileName($model->id, $model->base_name, $model->extension);

                    if (is_file($thumbnailFile)) {
                        unlink($thumbnailFile);
                    }
                }
            }
        }
    }

    /**
     *
     */
    public function actionUpdateImage()
    {
        $request = \Yii::$app->request;
        if ($request->isAjax) {
            $file_id = $request->post('image');
            $fetchUrl = $request->post('url');
            ImagesUploadModel::updateImage($file_id, $fetchUrl);
        }
    }
}
