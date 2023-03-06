<?php

namespace backend\actions;

use common\helpers\LanguageHelper;
use Yii;
use yii\base\Exception;
use yii\base\UnknownClassException;
use yii\base\UnknownMethodException;
use yii\web\NotFoundHttpException;
use yii\log\Logger;

/**
 * Class ActionCreate
 *
 * @package backend\actions
 */
class ActionCreate extends ActionCRUD
{
    /**
     * @var string
     */
    public $view = '//templates/create';

    /**
     * @inheritdoc
     * @throws UnknownClassException
     */
    public function init()
    {
        if ($this->modelClass === null) {
            throw new UnknownClassException(__CLASS__ . '::$modelClass must be set.');
        }

        $this->model = new $this->modelClass;
        $this->model->loadDefaultValues();

        if ($this->model === null) {
            throw new UnknownClassException($this->modelClass . 'must be exists.');
        }

        if (!method_exists($this->model, $this->formConfig)) {
            throw new UnknownMethodException("Method `{$this->formConfig}` not found");
        }
    }

    /**
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function run()
    {
        if ($this->model == null) {
            throw new NotFoundHttpException;
        }

        if ($this->saveModel()) {
            return $this->controller->redirect($this->getRedirect());
        } else {
            $this->controller->layout = $this->layout;
            return $this->controller->render($this->view, [
                'model' => $this->model,
                'formConfig' => $this->formConfig,
                'enableAjaxValidation' => $this->enableAjaxValidation
            ]);
        }
    }
}
