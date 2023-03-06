<?php

namespace api\components;

use yii\db\ActiveRecordInterface;
use yii\web\NotFoundHttpException;

class ViewAction extends \yii\rest\ViewAction
{
    /**
     * @param string $id
     * @return mixed|ActiveRecordInterface
     * @throws NotFoundHttpException
     */
    public function run($id)
    {
        $model = $this->findModel($id);
        return $model;
    }

    /**
     * @param string $id
     * @return mixed|ActiveRecordInterface
     * @throws NotFoundHttpException
     */
    public function findModel($id)
    {
        if ($this->findModel !== null) {
            return call_user_func($this->findModel, $id, $this);
        }
        /* @var $modelClass ActiveRecordInterface */
        $modelClass = $this->modelClass;
        $model = $modelClass::findOne(['alias'=>$id]);
        if (isset($model)) {
            return $model;
        }

        throw new NotFoundHttpException("Object not found: $id");
    }
}
