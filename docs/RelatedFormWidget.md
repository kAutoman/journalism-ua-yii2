Related Form Widget
===================================

Позволяет редактировать основную и связанные таблицы в одном месте. Для этого, на все таблицы, как обычно, создаются
CRUD и реляции. В связанных моделях можно использовать любые виджеты, включая загрузку картинок и файлов.
Для подключения виджета, в основной моделе нужно определить метод getRelatedFormConfig(), и указать названия реляций.
Также можно настроить uploadBehavior для загрузки любых файлов. 
Для ограничения количества связанных форм можно указать параметр limitMax и limitMin в конфиге (по умолчанию - 0 - лимит не учитывается). 
ВАЖНО! В связанной таблице в рулах необходимо убрать поле с foreign key из required и из getFormConfig(). Для работы
сортировки, в связанной таблице должно присутствовать поле position.
```php
public function getRelatedFormConfig()
{
    return [
        'tests' => [
            'relation' => 'tests', //имя реляции (всегда с маленькой буквы, от названия метода реляции убрать get)
        ],
        'anotherTests' => [
            'relation' => 'anotherTests',
            'uploadBehavior' => [ //если в связанных модлях требуется загружать файлы
                [
                    'attribute' => 'file_id',
                    'extensions' => ['png', 'gif', 'jpg', 'jpeg', 'ico', 'svg'],
                    'required' => false
                ]
            ],
        ],
        'testsWithLimit' => [
            'relation' => 'tests', //имя реляции (всегда с маленькой буквы, от названия метода реляции убрать get)
            'limitMax' => 4, // ограничение на количество связанных форм.
            'limitMin' => 2, // ограничение на количество связанных форм.
        ],
    ];
}
```
В getFormConfig() основой модели достаточно вызвать метод getRelatedFormConfig() и указать ключ массива с реляциями

```php
public function getFormConfig()
{
    return [
        'form-set' => [
            'Основные' => [
                'content' => [
                    'type' => ActiveFormBuilder::INPUT_TEXTAREA,
                ],
            ],
            'Опции' => [
                $this->getRelatedFormConfig()['tests']
            ],
            'Характеристики' => [
                $this->getRelatedFormConfig()['anotherTests']
            ],
        ]
    ];
}
```
Для использования uploadBehavior в виджете, необходимо в связанной моделе добавить код.
```php
    public function __get($name)
    {
        if (strpos($name, '[') === 0) {
            $name = substr($name, strpos($name, ']') + 1);
        }
        return parent::__get($name);
    }

    public function __set($name, $value)
    {
        if (strpos($name, '[') === 0) {
            $name = substr($name, strpos($name, ']') + 1);
        }
        parent::__set($name, $value);
    }
```
