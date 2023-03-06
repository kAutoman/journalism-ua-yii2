<?php

namespace api\components;

use yii\base\Model;
use yii\base\Arrayable;
use yii\rest\Serializer;
use yii\helpers\ArrayHelper;
use yii\data\DataProviderInterface;

/**
 * Class RestSerializer
 *
 * @package api\components
 */
class RestSerializer extends Serializer
{
    public function serialize($data)
    {
        if ($data instanceof Model && $data->hasErrors()) {
            return $this->serializeModelErrors($data);
        } elseif ($data instanceof Arrayable) {
            return $this->serializeModel($data);
        } elseif ($data instanceof DataProviderInterface) {
            return $this->serializeDataProvider($data);
        } elseif ($data instanceof BaseEntity) {
            return $this->serializeEntity($data);
        }

        return $data;
    }

    /**
     * Serializes a model object.
     * @param Arrayable $model
     * @return array the array representation of the model
     */
    protected function serializeEntity($model)
    {
        if ($this->request->getIsHead()) {
            return null;
        }
        return ArrayHelper::toArray($model);
    }
}
