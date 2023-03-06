SEO
===================================

настройка SEO страниц-списков
-----------------------------

В разделе Page SEO создать записи для необходимых страниц (изначально создана запись для страницы Home).
В common\models\PageSeo создать соответствующие константы, значение которых равно id записей.
Пример подключения SEO-бихевиора к AR-модели:
```php
'seo' => [
                'class' => \notgosu\yii2\modules\metaTag\components\MetaTagBehavior::className(),
                'defaultFieldForTitle' => 'label' //что бы заполнить title значением по-умолчанию, указываем attribute AR-модели
            ],
```
В контроллере на фронте вызывать метод ```PageSeo::registerSeo(PageSeo::ID_HOME);```

компонент редиректов
--------------------

Для управления редиректов средствами php необходимо перейти в адимнке "Configurations -> Redirects"
From - абсолютный url с которого происходит редирект
To - абсолютный url на который происходит редирект
Is active - статус редиректа(активный/не активный)

Редиректы кешируются в файловом кэше.
Кнопка "Clear cache" - очистка кэша.

ВАЖНО: В ```/frontend/helpers/Redirect```  метод ```toLowerCaseRedirect()``` делает редирект с url в котором есть буквы 
в верхнем регистре на url в нижнем регистре. Если есть необходимость не производить такой редирект(наличие в url токена,
имени файла и т. д.) нужно добавить соответствующие условие!!! 

robots.txt
----------

robots.txt редактируєтся в админке "Configurations -> Robots.txt"
Каждая опция должа быть добавлена с новой строки
```/site/robots``` - экшн для вывода robots.txt
Файла robots.txt в ```/frontend/web``` быть не должно.

sitemap.xml
-----------

Для генерации sitemap.xml используется:
[https://github.com/himiklab/yii2-sitemap-module](https://github.com/himiklab/yii2-sitemap-module)

ajax и внешние ссылки
---------------------

Для создания ajax ссылки в ```/frontend/helpers/ExtendedHtml``` сущестувует метод ```ajaxLink($text, $url = null, $options = [])```.
Он создает ссылку добавляя к ней клас ajax-link и rel ```noindex/nofollow```. Для клика на .ajax-link в ```frontend/web/js/frontend.js``` есть обработчик.

Для создания ссылки на внешний ресурс ```/frontend/helpers/ExtendedHtml``` сущестувует метод ```externalLink($text, $url = null, $options = [])```.
Он создает ссылку добавляя к ней rel ```noindex/nofollow```.

meta tags для шеров:
---------------------

```php
<?= \frontend\widgets\openGraphMetaTags\Widget::widget([
    'title' => 'Test title',
    'url' => Url::to(Url::current(['_pjax' => null]), true),
    'description' => 'Some test description',
    'image' => 'http://pbs.twimg.com/media/CaNtqoYUMAAENl3.jpg',
]); ?>
```

Html microdata:
---------------
Удобнее выводить в формате Json+ld через виджеты, как пример есть 2 готовых виджета для хлебных крошек и главной страницы -
Breadcrumbs и Website соответственно. Использовать виджеты можно практически в любом месте вывода, в любых вьюхах. Как пример:
````php
<?= \frontend\widgets\microdata\BreadcrumbsMicrodataWidget::widget(['homePageTitle' => 'Home']) ?>
````
Из коробки подключено оба вышеуказаных виджета в ``frontend/themes/basic/layouts/main.php``
Так как Schema.org содержит очень много описаний, а проекты по своему отличаются - микроразметку можно добавить через 
```\frontend\widgets\microdata\BaseMicrodataWidget::widget(['rawMicrodataData' => [...])``` где параметром служит массив 
конфига согласно Schema.org (либо отнаследовать виджет и там творить магию). 
