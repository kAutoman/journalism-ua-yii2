<?php

namespace backend\actions;

use backend\components\BackendModel;
use common\components\model\ActiveRecord;
use Yii;
use yii\base\Action;
use yii\base\UnknownClassException;
use yii\base\UnknownMethodException;

/**
 * Class ActionIndex
 *
 * @package backend\actions
 */
class ActionIndex extends Action
{

    /**
     * @var string
     */
    public $layout = '@app/layouts/main';

    /**
     * @var string
     */
    public $view = '//templates/index';

    /**
     * @var string
     */
    public $modelClass;

    /**
     * @var string
     */
    public $dataProvider = 'getDataProvider';

    /**
     * @var BackendModel|null
     */
    protected $model = null;

    /**
     * @var BackendModel|null
     */
    protected $searchModel = null;

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
        $this->searchModel = $this->model->getSearchModel();

        if (!method_exists($this->searchModel, $this->dataProvider)) {
            throw new UnknownMethodException("Method `{$this->dataProvider}` not found");
        }

        if ($this->model === null) {
            throw new UnknownClassException($this->modelClass . ' must be exists.');
        }
    }

    /**
     * @return string
     */
    public function run()
    {
        $dataProvider = call_user_func([$this->searchModel, $this->dataProvider], Yii::$app->getRequest()->getQueryParams());

        return $this->controller->render($this->view, [
            'searchModel' => $this->searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
