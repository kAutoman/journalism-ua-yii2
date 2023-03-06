<?php

namespace backend\actions;

use common\components\model\ActiveRecord;
use Yii;
use yii\base\Action;
use yii\base\UnknownClassException;
use yii\base\UnknownMethodException;
use yii\web\NotFoundHttpException;

/**
 * Class ActionView
 *
 * @package backend\actions
 */
class ActionView extends Action
{

    /**
     * @var string
     */
    public $layout = '@app/layouts/main';

    /**
     * @var string
     */
    public $view = '//templates/view';

    /**
     * @var string
     */
    public $modelClass;

    /**
     * @var ActiveRecord
     */
    protected $model = null;

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

        if ($this->model === null) {
            throw new UnknownClassException($this->modelClass . ' must be exists.');
        }

    }

    /**
     * @param int|string $id
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function run($id)
    {
        return $this->controller->render($this->view, [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the Menu model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param int|string $id
     *
     * @return \yii\db\ActiveRecord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        /** @var ActiveRecord $class */
        $class = $this->modelClass;
        $model = $class::find()->andWhere(['id' => $id])->one();
        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $model;
    }
}
