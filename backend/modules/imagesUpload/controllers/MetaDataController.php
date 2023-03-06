<?php

namespace backend\modules\imagesUpload\controllers;

use backend\components\BackendController;
use backend\modules\imagesUpload\models\FileMetaData;
use common\helpers\LanguageHelper;

/**
 * MetaDataController implements the CRUD actions for FileMetaData model.
 *
 * @property string $modelClass
 */
class MetaDataController extends BackendController
{
    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return FileMetaData::class;
    }

    public function actionGenerateForm($id = false): string
    {

        if (!$id || !is_numeric($id)) {
            return $this->renderAjax('//templates/no-file');
        }

        if (!request()->isAjax) {
            return $this->redirect('index');
        }
        $model = FileMetaData::getModelByFileId((int)$id);

        return $this->_renderMetaDataForm($model);
    }

    public function actionSaveForm($id = false): string
    {
        if (!$id || !is_numeric($id)) {
            return $this->renderAjax('//templates/no-file');
        }
        if (!request()->isAjax) {
            return $this->redirect('index');
        }

        $model = FileMetaData::getModelByFileId((int)$id);

        if ($model->load(request()->post()) && $model->save()) {
            $languages = LanguageHelper::getLanguageModels();
            foreach ($languages as $language) {
                \Yii::$app->cacheImage->delete($id . $language->locale);
            }
            return $this->renderAjax('//templates/meta-data-save-complete');
        }

        return $this->_renderMetaDataForm($model);
    }

    /**
     * @param FileMetaData $model
     *
     * @return string
     */
    private function _renderMetaDataForm(FileMetaData $model): string
    {
        return $this->renderAjax('meta-data-form', [
            'model' => $model,
            'action' => $model->getMetaDataSaveUrl(),
        ]);
    }
}
