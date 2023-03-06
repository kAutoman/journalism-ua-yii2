<?php

namespace api\actions;

use Yii;
use Closure;
use common\components\model\ActiveRecord;
use yii\base\Action;
use yii\data\ActiveDataProvider;

/**
 * Class ListAction
 * @package api\actions
 */
class ListAction extends Action
{
    /**
     * @var ActiveRecord
     */
    public $modelClass;

    /**
     * @var Closure|null
     */
    public $query = null;

    /**
     * @var int
     */
    public $pageSize = 20;

    /**
     * @var array
     */
    public $sort = [
        'defaultOrder' => [
            'position' => SORT_ASC,
        ]
    ];

    /**
     * @return ActiveDataProvider
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        $request = request();

        /** @var ActiveRecord $model */
        $model = Yii::createObject($this->modelClass);

        $query = $model::find();

        if (!is_null($this->query)) {
            $query = call_user_func($this->query, $query, $request);
        }

        return new ActiveDataProvider([
            'query' => $query,
            'sort' => $this->sort,
            'pagination' => [
                'pageSize' => $this->pageSize,
                'defaultPageSize' => $this->pageSize,
            ],
        ]);
    }
}

