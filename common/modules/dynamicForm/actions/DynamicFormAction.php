<?php

namespace common\modules\dynamicForm\actions;

use common\components\model\ActiveRecord;
use common\modules\dynamicForm\components\Model;
use common\modules\dynamicForm\interfaces\DynamicFormInterface;
use Yii;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\base\UnknownClassException;
use yii\base\UnknownMethodException;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\log\Logger;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Class ActionCreate
 *
 * @package common\modules\dynamicForm\actions
 */
class DynamicFormAction extends \backend\actions\ActionCRUD
{

    /**
     * Model class name.
     * Should extends [[ActiveRecord]]
     *
     * @var string
     */
    public $modelClass;

    /**
     * Model method name with {{FormBuilder}} form array config
     *
     * @var string
     */
    public $formConfig = 'getFormConfig';

    /**
     * Validate form with AJAX
     *
     * @var bool
     */
    public $enableAjaxValidation = false;

    /**
     * Model object
     *
     * @var ActiveRecord|DynamicFormInterface
     */
    protected $model = null;

    /**
     * @inheritdoc
     * @throws UnknownClassException
     * @throws InvalidConfigException
     */
    public function init()
    {
        if ($this->modelClass === null) {
            throw new UnknownClassException(__CLASS__ . '::$modelClass must be set.');
        }

        $this->model = new $this->modelClass;
        $this->model->loadDefaultValues();

        if (!$this->model instanceof DynamicFormInterface) {
            throw new InvalidConfigException('Model must implement `DynamicFormInterface`');
        }

        if ($this->model === null) {
            throw new UnknownClassException($this->modelClass . 'must be exists.');
        }

        if (!method_exists($this->model, $this->formConfig)) {
            throw new UnknownMethodException("Method `{$this->formConfig}` not found");
        }
    }

    public function run(?int $id = null)
    {
        $modelClass = $this->model;
        /** @var ActiveRecord|DynamicFormInterface $model */
        $model = $modelClass::findOneOrCreate(['id' => $id]);
        $model->loadDefaultValues();
        $config = $this->getDynamicFormActionConfig($model);
        /** @var ActiveRecord[][] $relModelsArray */
        $relModelsArray = $model->relatedModels;
        $model->relatedModels = [];
        $valid = true;
        $flag = false;
        $isLoadModels = false;
        $transaction = Yii::$app->getDb()->beginTransaction();
        try {
            // validate and save main model
            if ($isLoadModels = $this->loadModels($model)) {
                $valid = $model->validate() && $valid;
                $flag = $model->save(false);
            }
            foreach ($relModelsArray as $key => $relModels) {
                $relatedModel = $relModels[0];
                $relatedModelClassName = get_class($relatedModel);
                $deletedIDs = [];
                if ($isLoadModels) {
                    $oldIDs = ArrayHelper::map($relModels, 'id', 'id');
                    unset($oldIDs['']);
//                    d($oldIDs);
                    $relModels = Model::createMultiple($relatedModelClassName, Yii::$app->request->post(), $relModels);
                    $this->loadMultipleModels($relModels);
                    $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($relModels, 'id', 'id')));

                    // ajax validation
                    if (Yii::$app->getRequest()->getIsAjax()) {
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        return ArrayHelper::merge(
                            ActiveForm::validateMultiple($relModels),
                            ActiveForm::validate($model)
                        );
                    }
                    $valid = Model::validateMultiple($relModels) && $valid;

                    if ($valid) {
                        Model::saveRelModels($model, $relModels, $config, $key, $deletedIDs, $relatedModel, $flag);
                    } else {
                        /** @var $relModels ActiveRecord[] */
                        foreach ($relModels as $idx => $relModel) {
                            $relModel->addErrors($relModel->getFirstErrors());
                        }

                    }
                    if (!empty($deletedIDs)) {
                        $relModels = ArrayHelper::merge($relModels, [new $relatedModelClassName]);
                    }
                }
//                if (empty($deletedIDs) && !$relatedModel->isNewRecord) {
//                    $relModels = ArrayHelper::merge($relModels, [new $relatedModelClassName]);
//                }

                $model->relatedModels[$key] = $relModels;
                foreach ($relModels as $index => $item) {
                    $item->relModelIndex = $index;
                }
            }

            if ($flag && $valid) {
                $transaction->commit();
            } elseif ($isLoadModels) {
                throw new Exception('Models can not be saved');
            } else {
                $transaction->rollBack();
            }
        } catch (Exception $e) {
            $transaction->rollBack();

            if (Yii::$app->controller->action->id == 'create') {
                $this->setToNewRecordState($model);
            }
        }
        $view = '//templates/update';
        if ($model->isNewRecord) {
            $view = '//templates/create';
        }

        if ($flag && $valid) {
            $message = 'Record successfully updated!';
            if (Yii::$app->controller->action->id == 'create') {
                $message = 'Record successfully created!';
            }
            \Yii::$app->getSession()->setFlash('info', Yii::t('app', $message));

            return $this->controller->redirect($this->getRedirect());
        }

        return $this->controller->render($view, [
            'model' => $model,
            'formConfig' => $this->formConfig,
            'enableAjaxValidation' => $this->enableAjaxValidation
        ]);

    }
    /**
     * @param $model ActiveRecord
     */
    public function setToNewRecordState($model)
    {
        $model->isNewRecord = true;
        /** @var ActiveRecord[] $relModels */
        foreach ($model->relatedModels as $relatedModels) {
            foreach ($relatedModels as $relatedModel) {
                $relatedModel->isNewRecord = true;
            }
        }
    }

    /**
     * @param DynamicFormInterface|ActiveRecord $model
     * @return array
     */
    public function getDynamicFormActionConfig(DynamicFormInterface $model): array
    {
        $formConfig = $model->getDynamicFormConfig();
        $data = [];
        foreach ($formConfig as $config) {
            $relation = $config['relation'];
            $className = $model->getRelation($relation)->modelClass;
            $relatedModels = $model->$relation;
            if ($relatedModels && !is_array($relatedModels)) {
                $relatedModels = $relatedModels->all();
            }
            $model->relatedModels[$relation] = $relatedModels ? $relatedModels : [new $className];
            $data[$relation] = $config;
        }

        return $data;
    }

    /**
     * @param \yii\db\ActiveRecord $model
     *
     * @return bool
     */
    public function loadModels($model)
    {
        $loaded = true;
        $loaded = $model->load(Yii::$app->request->post()) && $loaded;
        return $loaded;
    }

    /**
     * @param \yii\db\ActiveRecord[] $models
     * @return bool
     * @throws InvalidConfigException
     */
    public function loadMultipleModels($models)
    {
        $loaded = true;
        foreach ($models as $index => $model) {
            $loaded = $model->load(Yii::$app->request->post($model->formName())[$index], '') && $loaded;
            $model->loadDefaultValues();
        }

        return $loaded;
    }
}
