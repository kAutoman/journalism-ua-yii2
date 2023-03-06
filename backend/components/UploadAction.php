<?php
/**
 * Created by PhpStorm.
 * User: metal
 * Date: 02.10.15
 * Time: 17:02
 */

namespace backend\components;

use metalguardian\fileProcessor\helpers\FPM;
use Yii;
use yii\base\DynamicModel;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * Class UploadAction
 */
class UploadAction extends \vova07\imperavi\actions\UploadAction
{
    /**
     * @var string Model validator name
     */
    private $_validator = 'image';

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->uploadOnlyImage !== true) {
            $this->_validator = 'file';
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (Yii::$app->request->isPost) {
            $file = UploadedFile::getInstanceByName($this->uploadParam);
            $model = new DynamicModel(compact('file'));
            $model->addRule('file', $this->_validator, $this->validatorOptions)->validate();

            if ($model->hasErrors()) {
                $result = [
                    'error' => $model->getFirstError('file')
                ];
            } else {

                $imageId = FPM::transfer()->saveUploadedFile($file);

                $result = [
                    'filelink' => FPM::originalSrc($imageId),
                ];
                if ($this->uploadOnlyImage !== true) {
                    $data = FPM::transfer()->getData($imageId);
                    $result['filename'] = $data->base_name . '.' . $data->extension;
                }
            }
            Yii::$app->response->format = Response::FORMAT_JSON;

            return $result;
        } else {
            throw new BadRequestHttpException('Only POST is allowed');
        }
    }
}
