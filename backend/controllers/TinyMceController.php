<?php

namespace backend\controllers;

use Yii;
use yii\web\Response;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\base\DynamicModel;
use yii\web\BadRequestHttpException;

/**
 * Class TinyMceController
 *
 * @package backend\controllers
 */
class TinyMceController extends Controller
{
    /**
     * @inheritdoc
     */
    public $enableCsrfValidation = false;

    /**
     * @var string Variable's name that Redactor sent upon image/file upload.
     */
    public $uploadParam = 'file';
    /**
     * @var string Path to directory where files will be uploaded
     */
    public $path = 'uploads/redactor';

    /**
     * @var string URL path to directory where files will be uploaded
     */
    public $url = '/uploads/redactor/';

    /**
     * TinyMCE upload action.
     *
     * @return string|array json
     * @throws BadRequestHttpException
     */
    public function actionUpload()
    {
        if (Yii::$app->getRequest()->getIsPost()) {
            $file = UploadedFile::getInstanceByName('file');
            $model = new DynamicModel(compact('file'));
            $model->addRule('file', 'image')->validate();

            if ($model->hasErrors()) {
                $result = ['error' => $model->getFirstError('file')];
            } else {
                if ($model->file->extension) {
                    $model->file->name = uniqid() . '.' . $model->file->extension;
                }
                if ($model->file->saveAs($this->path . '/' . $model->file->name)) {
                    $result = ['location' => $this->url . $model->file->name];
                } else {
                    $result = ['error' => Yii::t('back/app', 'Can not upload file')];
                }
            }
            Yii::$app->response->format = Response::FORMAT_JSON;

            return $result;
        } else {
            throw new BadRequestHttpException('Only POST is allowed');
        }
    }
}
