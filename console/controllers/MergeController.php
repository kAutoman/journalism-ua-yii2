<?php

namespace console\controllers;

use common\models\CompetitionRequest;
use yii\console\Controller;

class MergeController extends Controller
{
    public function actionIndex()
    {
        $list = [
            1 => 'Найкраще інтерв’ю',
            2 => 'Найкращий репортаж',
            3 => 'Найкраще новинне висвітлення резонансної події',
            4 => 'Найкраща аналітика',
            5 => 'Найкраще розслідування',
            6 => 'Найкраща публіцистика',
            7 => 'Спецномінація Конструктивна журналістика',
        ];

        /** @var CompetitionRequest[] $models */
        $models = CompetitionRequest::find()->all();

        foreach ($models as $model) {
            $key = (int)array_search($model->nomination, $list);

            $model->nomination_id = $key;
            $model->save(false, ['nomination_id']);
        }
    }
}
