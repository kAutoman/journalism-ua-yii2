<?php

namespace common\modules\fileManager\controllers;

use trntv\filekit\actions\DeleteAction;
use common\modules\fileManager\actions\UploadAction;
use trntv\filekit\actions\ViewAction;
use yii\web\Controller;
use yii\web\Response;

/**
 * Class FileController
 *
 * @package common\modules\fileManager\controllers
 */
class FileController extends Controller
{
    public function actions()
    {
        return [
            'upload'=>[
                'class' => UploadAction::class,
                'multiple' => true,
                'disableCsrf' => true,
                'responseFormat' => Response::FORMAT_JSON,
                'responsePathParam' => 'path',
                'responseBaseUrlParam' => 'base_url',
                'responseUrlParam' => 'url',
                'responseDeleteUrlParam' => 'delete_url',
                'responseMimeTypeParam' => 'type',
                'responseNameParam' => 'name',
                'responseSizeParam' => 'size',
                'deleteRoute' => 'delete',
                'fileStorage' => 'fileStorage', // Yii::$app->get('fileStorage')
                'fileStorageParam' => 'fileStorage', // ?fileStorage=someStorageComponent
                'sessionKey' => '_uploadedFiles',
                'allowChangeFilestorage' => false,
                'validationRules' => [
                    //...
                ],
           ],
            'delete' => ['class' => DeleteAction::class],
            'view' => ['class' => ViewAction::class]
        ];
    }
}
