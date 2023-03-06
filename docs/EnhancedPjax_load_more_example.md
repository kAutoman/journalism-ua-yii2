Enhanced PJAX widget 
====================

**EnhancedPjax** это немного усовершенствованый стандартный виджет Pjax,
который дополнительно поддерживает две новых опции:

1. `events` это шорткат вынесеный из `clientOptions` для указания обработчиков событий pjax плагина;
2. `zones` новая опция, позволяет по разному обрабатывать разные зоны HTML ответа.

Пример использования **EnhancedPjax** вмести с **OffsetPagination** компонентом и **OffsetLinkPager** виджетом:


#### Controller:


```php
<?php

namespace frontend\modules\teacher\controllers;

use yii\data\ActiveDataProvider;
use common\components\OffsetPagination;
use frontend\modules\test\models\Test;
use frontend\components\FrontendController;

class TestController extends FrontendController
{
    /**
     * @var int number of items per page.
     */
    const ITEMS_PER_PAGE = 10;

    /**
     * Index action.
     *
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Test::find()->isPublished(),
            'pagination' => [
                'class' => OffsetPagination::class,
                'pageSize' => self::ITEMS_PER_PAGE,
                'defaultPageSize' => self::ITEMS_PER_PAGE,
            ],
        ]);

        return $this->render('index', compact('dataProvider'));
    }
}
```


#### View: 


```php
<?php

use yii\widgets\ListView;
use common\widgets\EnhancedPjax;
use common\widgets\OffsetLinkPager;

/**
 * @var $this yii\web\View the view component.
 * @var $dataProvider yii\data\ActiveDataProvider data provider with custom pagination. 
 */

EnhancedPjax::begin([
    'id' => 'pjax-wrapper',
    'zones' => [
        '#list-view-items' => EnhancedPjax::ZONE_APPEND,
        '#list-view-pager' => EnhancedPjax::ZONE_REPLACE,
    ],
    'events' => [
        'complete' => 'function(xhr, textStatus, options) {
            setTimeout(function() {
                $(".item").addClass("animated");
            }, 200);
        }'
    ],
]);

echo ListView::widget([
    'dataProvider' => $dataProvider,
    'layout' => '<div id="list-view-items">{items}</div>{pager}',
    'itemView' => '_item',
    'options' => ['tag' => false],
    'itemOptions' => ['tag' => false],
    'pager' => [
        'class' => OffsetLinkPager::class,
        'options' => ['tag' => 'div', 'id' => 'list-view-pager'],
        'linkLabel' => Yii::t('front/speaker', 'Show more'),
        'linkOptions' => ['class' => ['btn' , 'btn__transparent']]
    ],
]);

EnhancedPjax::end();
```


**Пояснение примера:**

После того как придёт ответ от сервера, в существующий на странице тег с идентификатором `#list-view-items` будут добавлены (в конец) **дочерние** элементы того же тега с респонса.
А тег с идентификатором `#list-view-pager` будет полностью заменён соответствующим из респонса.

В примере используються кастомный виджет `OffsetLinkPager` и компонент `OffsetPagination`.
Сделаны они для упрощения реализации такой фичи как дозагрузка контента по кнопке **Load more**.
