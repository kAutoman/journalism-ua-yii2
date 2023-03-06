<?php

namespace console\controllers;

use common\models\lang\MemberItemLang;
use common\models\MemberItem;
use yii\console\Controller;

/**
 * Class NominationController
 * @package console\controllers
 */
class NominationController extends Controller
{
    public function actionCreate()
    {
        MemberItem::deleteAll();

        $list = [
            1 => "Найкраще інтерв’ю",
            2 => "Найкращий репортаж",
            3 => "Найкраще новинне висвітлення резонансної події",
            4 => "Найкраща аналітика",
            5 => "Найкраще розслідування",
            6 => "Найкраща публіцистика",
            7 => "Спецномінація \"Конструктивна журналістика\"",
        ];

        $mainTable = MemberItem::tableName();
        $trTable = MemberItemLang::tableName();

        foreach ($list as $id => $item) {
            db()->createCommand()->insert($mainTable, [
                'id' => $id,
                'published' => 1,
                'position' => 0,
                'created_at' => time(),
                'updated_at' => time(),
            ])->execute();
            
            db()->createCommand()->insert($trTable, [
                'model_id' => $id,
                'language' => 'uk',
                'label' => $item,
                'content' => null,
            ])->execute();
        }
    }
}
