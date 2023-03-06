<?php

namespace api\actions;

use Closure;
use common\components\model\ActiveRecord;
use yii\base\Action;
use yii\web\BadRequestHttpException;
use yii\web\UnprocessableEntityHttpException;

/**
 * Class ActionSubmit
 *
 * @property ActiveRecord $modelClass
 * @property Closure $callback
 *
 * @package api\actions
 */
class ActionSubmit extends Action
{
    /**
     * @var ActiveRecord
     */
    public $modelClass;
    /**
     * @var Closure
     */
    public $callback;

    /**
     * @return array|mixed
     * @throws BadRequestHttpException
     * @throws UnprocessableEntityHttpException
     */
    public function run()
    {
        response()->setStatusCode(422);
        exit();
        if (request()->getIsPost() || request()->getIsOptions()) {
            $post = request()->post();
            /** @var ActiveRecord $model */
            $model = new $this->modelClass();

            if (!$model->load($post, '')) {
                throw new UnprocessableEntityHttpException('POST data is not correct or empty');
            }
            if (!$model->validate()) {
                response()->setStatusCode(422);
            }

            if ($model->save()) {
                return call_user_func($this->callback, $model);
            }

            return $model->getFirstErrors();
        }
        throw new BadRequestHttpException('Only POST and OPTIONS methods are allowed');
    }
}
