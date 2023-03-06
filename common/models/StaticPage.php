<?php


namespace common\models;


use common\modules\config\application\components\AggregateMaker;
use common\modules\config\application\entities\Home;

class StaticPage
{
    CONST PAGE_HOME = 'home';

    public static function getAggregateMaker($alias)
    {
        return isset(self::getListClases()[$alias]) ? (new AggregateMaker(self::getListClases()[$alias]))->make() : null;
    }

    public static function getListClases()
    {
        return [
            self::PAGE_HOME => Home::class
        ];
    }

    public static function getListStaticPages()
    {
        return [
            self::PAGE_HOME => bt('Home page', 'back/menu')
        ];
    }

    public static function getMenuList()
    {
        $items = [];
        foreach (self::getListStaticPages() as $key => $label) {
            $items[] = [
                'label' => $label,
                'url' => ['/config/' . $key]
            ];
        }
        return $items;
    }
}
