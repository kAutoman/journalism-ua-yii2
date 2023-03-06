<?php

namespace backend\modules\page\models;

use yii\helpers\Url;
use common\behaviors\TranslatedBehavior;
use paulzi\nestedsets\NestedSetsBehavior;

/**
 * Class PagesTree
 *
 * Model for clear tree view, without "heavy" logic
 *
 * @package backend\modules\page\models
 */
class PagesTree extends Page
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'translated' => [
                'class' => TranslatedBehavior::class,
                'translateAttributes' => $this->getLangAttributes()
            ],
            'tree' => [
                'class' => NestedSetsBehavior::class,
                'treeAttribute' => 'root',
            ],
        ];
    }

    public static function getPagesTree()
    {
        /**
         * @var NestedSetsBehavior[]|self[] $models
         * @todo need more query optimization
         */
        $models = self::find()
            ->roots()
            ->joinWith(['currentTranslate'])
            ->addOrderBy('root, lft')
            ->all();

        return self::normalizeItems($models);
    }

    /**
     * @var Page[] | NestedSetsBehavior[] $models
     *
     * @return array
     */
    private static function normalizeItems($models)
    {
        $data = [];
        foreach ($models as $key => $model) {
            /** @var NestedSetsBehavior|self $model*/
            $model->populateTree();
            $children = $model->children;
            $data[$key] = [
                'text' => $model->label,
                'href' => Url::to(['update', 'id' => $model->id]),
                'icon' => !$model->published ? 'fa fa-eye-slash' : '',
            ];
            if ((int) request()->get('id') === $model->id) {
                $data[$key]['state'] = [
                    'selected' => true,
                    'expanded' => true
                ];
            }
            if (!empty($children)) {
                $data[$key]['nodes'] = self::normalizeItems($children);
            }

        }

        return $data;
    }
}
