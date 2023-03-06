Конфигуратор
===================================

Позволяет создавать отдельные формы для конфига, т.е. теперь не надо создавать отдельную таблицу на каждую страницу, 
достаточно сгенерировать модель и контроллер через gii в разделе Static Page Model Generator. 
Для этого нужно заполнить обязательные поля:

Model namespace - namespace модели для бэкэнда;

Model Class Name - название генерируемой модели;

Controller Class - полное имя контроллера для бэкэнда;

Title - заголовок модели для отображения на странице редактирования в бэкэнде;

Keys - ключи с типами и описанием. Названия ключей (Key) должны быть уникальны в пределах одной модели. Типы (Field type)
определяют формат полей для ввода. Описания (Field description) подписывают поля на странице редактирования в бэкэнде.
В поле Rule описывается правило валидации согласно общим правилам, но без указания названия атрибута. Т.е. нужно вписывать
только правило в кавычках. Например, 'email', добавит email-валидатор к данному ключу, а 'string', 'max' => 10 ограничит 
колличество вводимых символов. В Rule не нужно вводить правило 'required', т.к. для этого случая есть галочка Is Required. 

ВАЖНО! Без заполнения Is Required и Rule, будут действовать правила по умолчанию, т.е. поле необязательное для заполения, 
а его значение валидируется в зависимости от типа (подробней смотри rules() в backend\modules\configuration\models\Configurator).
Для каждого ключа можно заполнить только одно правило, а если еще поставлена галочка Is Required, то сгенерируется всего
два правила для данного ключа. Если нужно добавить дополнительные правила, то это можно сделать вручную в сгенерированной 
моделе в методе getFormRules().

Кроме этого, есть возоможность сгенерировать seo behavior, imageUploadWidget с кроппером и заготовку для подключения
RelatedFormWidget. Соответственные галочки необходимо поставить в самом генераторе.

##Особенности использования

Для backend

Для настройки расположения полей в форме и табов, используется стнадартный метод getFormConfig(). Для этого достаточно
указать названия ключей.
Подключение imageUploadWidget работает по стандартному [прниципу множественной загрузки](docs/File_upload.md).
```php
/**
* @return array
*/
public function getFormConfig()
{
    $config = [
        'form-set' => [
            'main' => [
                CommonStaticPage::KEY_1,
                CommonStaticPage::KEY_2,
                'titleImage' => [
                     'type' => ActiveFormBuilder::INPUT_RAW,
                     'value' => ImageUpload::widget([
                     'model' => $this,
                     'attribute' => 'titleImage',
                         //'saveAttribute' => EntityToFile::TYPE_ARTICLE_TITLE_IMAGE, //TODO Создать контанту и раскомментировать
                         //'aspectRatio' => 300/200, //Пропорция для кропа
                         'multiple' => false, //Вкл/выкл множественную загрузку
                     ])
                 ]
            ],
            'Tab name' => [
                $this->getRelatedFormConfig()['relationName']
            ]
        ]
    ];

    return $config;
}
```

Подключение RelatedFormWidget работает также по стандартному [прниципу](docs/RelatedFormWidget.md).

```php
/**
* @return array
*/
public function getFormConfig()
{
    $config = [
        'form-set' => [
            'Tab name' => [
                $this->getRelatedFormConfig()['relationName']
            ]
        ]
    ];

    return $config;
}

/**
* @return array
*/
public function getRelatedFormConfig()
{
    $config = [
        'relationName' => [
            'relation' => 'relationName',
        ],
    ];

    return $config;
}

/**
* @return ActiveQueryInterface
*/
public function getRelationName()
{
    return $this->hasMany(ModelName::className(), ['foreign_key' => 'id'])->orderBy('position');
}
```
Контроллер и необходимая коммоновская модель генерируются автоматически и не требуют ручного вмешательства.

Для работы типа поля dropdown необходимо переопределить метод
```php
/**
 * @inheritdoc
 */
public function getDropdownItems()
{
    return [];
}
```
даный метод должен возвращать массив ключами которого являются названия полей,
а значениями массивы с элементами списка
```php
/**
 * @inheritdoc
 */
public function getDropdownItems()
{
    $routeList = RouteHelper::getRoutesList();
    return [
        CommonHomePage::DESCRIPTION_LINK_ROUTE => $routeList,
        CommonHomePage::CITY_RESORT_LINK_ROUTE => $routeList,
        CommonHomePage::APARTMENTS_LINK_ROUTE => $routeList,
        CommonHomePage::PRIVATE_EVENTS_LINK_ROUTE => $routeList,
    ];
}
```

Для frontend

В экшене статической страницы небоходимо создать экземпляр коммоновской модели и вызвать метод get() (так модель
наполняется данными). Для подключения SEO небоходимо вызвать метод ConfigurationMetaTagRegister::register($model)
```php
public function actionIndex()
{
    $model = (new StaticPageSample())->get();
    ConfigurationMetaTagRegister::register($model);

    return $this->render('index', ['model' => $model]);
}
```
Во вьюхе для вывода данных из модели, достаточно обратиться к конкретному атрибуту модели, который автоматически
сгенерировался на этапе gii.
```php
$model->key2
$model->key3
```
