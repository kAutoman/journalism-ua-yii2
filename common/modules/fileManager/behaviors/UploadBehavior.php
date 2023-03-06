<?php

namespace common\modules\fileManager\behaviors;

use trntv\filekit\behaviors\UploadBehavior as BaseUploadBehavior;
use common\modules\fileManager\models\File;
use yii\helpers\ArrayHelper;

/**
 * Class UploadBehavior
 *
 * @package common\modules\fileManager\behaviors
 */
class UploadBehavior extends BaseUploadBehavior
{
    public $multiple = true;
    public $pathAttribute = 'path';
    public $baseUrlAttribute = 'base_url';
    public $typeAttribute = 'type';
    public $sizeAttribute = 'size';
    public $nameAttribute = 'name';
    public $orderAttribute = 'order';
    public $imgAltAttribute = 'img_alt';
    public $imgTitleAttribute = 'img_title';
    /**
     * @return void
     */
    public function afterFindMultiple()
    {
        $models = $this->owner->{$this->uploadRelation};
        $fields = $this->fields();
        $data = [];
        foreach ($models as $k => $model) {
            /* @var $model \yii\db\BaseActiveRecord */
            $file = [];
            foreach ($fields as $dataField => $modelAttribute) {
                $file[$dataField] = $model->hasAttribute($modelAttribute)
                    ? ArrayHelper::getValue($model, $modelAttribute)
                    : null;
            }
            if ($file['path']) {
                $data[$k] = $this->enrichFileData($file);
            }
        }
        $this->owner->{$this->attribute} = $data;
    }
    
    /**
     * @throws \Exception
     */
    public function afterUpdateMultiple()
    {
        $filesPaths = ArrayHelper::getColumn($this->getUploaded(), 'path');
        $models = $this->owner->getRelation($this->uploadRelation)->all();

        $modelsPaths = ArrayHelper::getColumn($models, $this->getAttributeField('path'));
        $newFiles = $updatedFiles = [];
        foreach ($models as $model) {
            $path = $model->getAttribute($this->getAttributeField('path'));
            if (!in_array($path, $filesPaths, true) && $model->delete()) {
                $this->getStorage()->delete($path);
            }
        }
        foreach ($this->getUploaded() as $file) {
            if (!in_array($file['path'], $modelsPaths, true)) {
                $newFiles[] = $file;
            } else {
                $updatedFiles[] = $file;
            }
        }
        $this->saveFilesToRelation($newFiles);
        $this->updateFilesInRelation($updatedFiles);
    }

    /**
     * @return array
     */
    protected function getUploaded()
    {
        $files = $this->owner->{$this->attribute};
        return $files ?: [];
    }
    
    /**
     * @param array $files
     */
    protected function saveFilesToRelation($files)
    {
        $modelClass = $this->getUploadModelClass();
//        var_dump($_POST);die;
        foreach ($files as $pos => $file) {
            /** @var File $model */
            $model = new $modelClass;
            $model->setScenario($this->uploadModelScenario);
            $model = $this->loadModel($model, $file);
            $model->entity_model_name = $this->owner->formName();
            $model->entity_model_attribute = $this->attribute;
            if ($this->getUploadRelation()->via !== null) {
                $model->save(false);
            }
            $this->owner->link($this->uploadRelation, $model);
        }
    }

    /**
     * @param array $files
     */
    protected function updateFilesInRelation($files)
    {
        $modelClass = $this->getUploadModelClass();
        foreach ($files as $file) {
            $model = $modelClass::findOne([$this->getAttributeField('path') => $file['path']]);
            if ($model) {
                $model->setScenario($this->uploadModelScenario);
                $model = $this->loadModel($model, $file);
                $model->save(false);
            }
        }
    }

    /**
     * @return array
     */
    public function fields()
    {
        $fields = [
            $this->attributePathName ? : 'path' => $this->pathAttribute,
            $this->attributeBaseUrlName ? : 'base_url' => $this->baseUrlAttribute,
            'type' => $this->typeAttribute,
            'size' => $this->sizeAttribute,
            'name' => $this->nameAttribute,
            'order' => $this->orderAttribute,
            'img_alt' => $this->imgAltAttribute,
            'img_title' => $this->imgTitleAttribute,
        ];

        if ($this->attributePrefix !== null) {
            $fields = array_map(function ($fieldName) {
                return $this->attributePrefix . $fieldName;
            }, $fields);
        }

        return $fields;
    }

    /**
     * @return void
     */
    public function afterDelete()
    {
        /** @var \common\components\model\ActiveRecord $model */
        $model = $this->getUploadModelClass();
        $model::deleteAll(['path' => $this->deletePaths]);

        parent::afterDelete();
    }
}
