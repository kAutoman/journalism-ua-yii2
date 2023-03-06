
Vintage Yii2 Application
===================================

1. [Установка](#markdown-header-install)
2. [Кастомный CRUD](#markdown-header-crud)
3. [Полезности](#markdown-header-useful)
4. [Готовые модули](#markdown-header-modules)
5. [SEO](#markdown-header-seo)
7. [Backup данных](#markdown-header-backup)
9. [Конструктор](docs/Builder.md)
10. [Функционал заполнения alt тегов в ImageUpload виджете](docs/image-upload-metadata.md)
11. [Сжатие и объедение стилей и скриптов дла production окружения](docs/compress_and_concat.md)

### Install

1.Клонируем движок

```bash
$ git clone git@bitbucket.org:vintageua/engine.git project-name
$ cd project-name
```

для получения обновлений движка

```bash
$ git remote add upstream git@bitbucket.org:vintageua/engine.git
```

2.Инициализация приложения

```bash
./init
```

3.Установка всех зависимостей

```bash
composer install
```

4.Конфигурируем подключение к БД, в `common/config/main-local.php` и поднимаем миграции

```
./yii migrate
```

### Generators

1. [Advanced Module Generator](docs/generators/AdvancedModuleGenerator.md)
2. [Migration Generator](docs/generators/MigrationGenerator.md)
3. [Static Page Generator (TBD)](docs/generators/StaticPageGenerator.md)
4. [Builder Model Generator (TBD)](docs/generators/BuilderModelGenerator.md)

### Core modules

1. [Configuration (TBD)](docs/modules/configurator.md)
2. [Builder (TBD)](docs/modules/builder.md)
3. [Dynamic form (TBD)](docs/modules/dynamicForm.md)
4. [Page (TBD)](docs/modules/pages.md)
5. [Google Map](docs/modules/map.md)

### Useful

1.[Загрузка и отображение файлов/изображений](docs/File_upload.md)

2.[Работа с данными при ajax-запросах](docs/Ajax_features.md)

3.Работа с переводами:

Для добавления нового языка, вам нужно добавить его в админке Translations->Language и в common/main.php добавить его:
```php
'i18n' => [
            'class' => 'Zelenin\yii\modules\I18n\components\I18N',
            'languages' => [
                //тут добавляем code нужного языка
                'ru',
                'en'
            ]
        ],
```
для того что бы перевод для данного языка появился в Translations.

При использовании `Yii::t('app', 'your_key')`, ключ перевода будет автоматически добавлен в БД в таблицу для переводов.
Заполнить нужные переводы для ключа можна в админке, раздел Translations.

6.[Configuration form builder](docs/Configuration.md)

7.[Related form widget](docs/RelatedFormWidget.md) - Простой способ редактирования основной таблицы и связанных в одном месте.

8.[Создание табов в редактировании модели](docs/AddTabs.md)

9.[Сохранение multiple dropdown](docs/Has_many_behavior.md)

10.[Деплой на production](docs/ProductionDeployment.md)

### SEO

[SEO](docs/SEO.md)
