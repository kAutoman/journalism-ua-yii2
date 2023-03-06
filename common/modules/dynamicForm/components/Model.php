<?php

namespace common\modules\dynamicForm\components;

use common\components\model\ActiveRecord;
use Yii;
use yii\base\Model as BaseModel;
use yii\helpers\ArrayHelper;

/**
 * Class Model
 *
 * @package common\modules\dynamicForm\components
 */
class Model extends BaseModel
{
    /**
     * Creates and populates a set of models.
     *
     * @param string $modelClass
     * @param $post
     * @param array $multipleModels
     * @return array
     */
    public static function createMultiple($modelClass, $post, $multipleModels = [])
    {
        $model = new $modelClass;
        $formName = $model->formName();
        $post = isset($post[$formName]) ? $post[$formName] : null;
        $models = [];

        if (!empty($multipleModels)) {
            $keys = array_keys(ArrayHelper::map($multipleModels, 'id', 'id'));
            $multipleModels = array_combine($keys, $multipleModels);
        }

        if ($post && is_array($post)) {
            foreach ($post as $i => $item) {
                if (isset($item['id']) && !empty($item['id']) && isset($multipleModels[$item['id']])) {
                    $models[$i] = $multipleModels[$item['id']];
                } else {
                    $models[$i] = new $modelClass;
                }
            }
        }

        unset($model, $formName, $post);

        return $models;
    }

    public static function saveRelModels($model, $relModels, $config, $key, $deletedIDs, $relatedModel, &$flag)
    {
        if ($flag) {
            if (!empty($deletedIDs)) {
                $relatedModel::deleteAll(['id' => $deletedIDs]);
            }
            $i = 0;
            foreach ($relModels as $modelRel) {
                $relatedAttribute = key($model->getRelation($config[$key]['relation'])->link);
                $modelRel->$relatedAttribute = $model->id;
                if ($modelRel->hasAttribute('position')) {
                    $modelRel->position = $i++;
                }
                if (!($flag = $modelRel->save(false) && $flag)) {
                    break;
                }
            }
        }
    }
}
