<?php

namespace backend\actions;

use Closure;
use Yii;
use yii\base\Action;
use common\components\model\ActiveRecord;
use yii\db\StaleObjectException;
use yii\log\Logger;
use yii\web\NotFoundHttpException;

/**
 * Class ActionCRUD
 *
 * @package backend\actions
 */
class ActionCRUD extends Action
{
    /**
     * Base layout
     *
     * @var string
     */
    public $layout = '@app/views/layouts/main';

    /**
     * Default view
     *
     * @var string
     */
    public $view = '//templates/index';

    /**
     * Model class name.
     * Should extends [[ActiveRecord]]
     *
     * @var string
     */
    public $modelClass;

    /**
     * Redirect action
     *
     * @var string|array|Closure
     */
    public $redirect = ['index'];

    /**
     * Scenario name for form validation
     *
     * @var string
     */
    public $scenario = 'default';

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
     * @var ActiveRecord
     */
    protected $model = null;

    /**
     * Find model by primary key
     *
     * @param mixed $id
     * @return ActiveRecord
     * @throws NotFoundHttpException
     */
    public function findModel($id): ActiveRecord
    {
        $modelClass = $this->model;
        $modelClass->setScenario($this->scenario);
        $model = $modelClass::find()->andWhere(['id' => $id])->one();
        if ($model === null) {
            throw new NotFoundHttpException('Model not found');
        }

        return $model;
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

    /**
     * Model getter
     *
     * @return ActiveRecord
     */
    public function getModel(): ActiveRecord
    {
        return $this->model;
    }

    /**
     * Save model to Db
     *
     * @return bool
     */
    public function saveModel(): bool
    {
        $model = $this->model;
        $model->setScenario($this->scenario);
        try {
            if ($model->load(request()->post())) {
                if ($model->save()) {
                    return true;
                }
                Yii::getLogger()->log($model->getFirstErrors(), Logger::LEVEL_ERROR);
            }
        } catch (StaleObjectException $e) {
            session()->addFlash('warning', bt('Your version is out of date. Please, reload page.'));
        }


        return false;
    }
}
