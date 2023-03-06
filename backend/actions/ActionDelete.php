<?php

namespace backend\actions;

use backend\components\BackendModel;
use Closure;
use Yii;
use yii\base\Action;
use yii\base\UnknownClassException;
use yii\base\UnknownPropertyException;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;
use common\components\model\ActiveRecord;

/**
 * Class ActionDelete
 *
 * @package backend\actions
 */
class ActionDelete extends Action
{

    /**
     * @var string
     */
    public $layout = '@app/layouts/main';

    /**
     * @var array|Closure
     */
    public $redirect = ['index'];

    /**
     * @var string
     */
    public $modelClass;

    /**
     * @var bool
     */
    public $safeDelete = false;

    /**
     * @var array
     */
    public $safeDeleteOptions = [
        'attribute' => 'deleted',
        'timeAttribute' => 'deleted_at'
    ];

    /**
     * @var ActiveRecord
     */
    protected $model = null;

    /**
     * @inheritdoc
     * @throws UnknownClassException
     * @throws UnknownPropertyException
     */
    public function init()
    {
        if ($this->modelClass === null) {
            throw new UnknownClassException(__CLASS__ . '::$modelClass must be set.');
        }
        $this->model = new $this->modelClass;
        if ($this->model === null) {
            throw new UnknownClassException($this->modelClass . ' must be exists.');
        }
        if ($this->safeDelete) {
            $attribute = $this->safeDeleteOptions['attribute'];
            $timeAttribute = $this->safeDeleteOptions['timeAttribute'];
            if (!$this->model->hasAttribute($attribute) || !$this->model->hasAttribute($timeAttribute)) {
                throw new UnknownPropertyException("`{$attribute}` or `{$timeAttribute}` not found in current model");
            }
        }
    }

    /**
     * @param int $id
     *
     * @return string
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function run(int $id)
    {
        /** @var BackendModel $searchModel */
        $model = $this->model::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException();
        }
        if ($this->safeDelete) {
            $model->updateAttributes([
                $this->safeDeleteOptions['attribute'] => 1,
                $this->safeDeleteOptions['timeAttribute'] => formatter()->asTimestamp('now'),
            ]);
        } else {
            $model->delete();
        }
        Yii::$app->getSession()->setFlash('danger', Yii::t('back/app', 'Record successfully deleted!'));

        return $this->controller->redirect($this->getRedirect());
    }

    /**
     * Manage redirection after form save
     *
     * @return mixed
     */
    public function getRedirect()
    {
        $redirect = $this->redirect;
        if (is_array($redirect)) {
            $r = $redirect;
        } elseif ($redirect instanceof Closure) {
            $r = $redirect();
        } else {
            $r = [$this->redirect, 'id' => $this->model->getPrimaryKey()];
        }
        return $r;
    }
}
