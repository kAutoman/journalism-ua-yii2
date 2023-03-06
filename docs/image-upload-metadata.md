Функционал по добавлению метаданных к загруженым файлам
=

1. Изменения в `backend\modules\imagesUpload\widgets\imagesUpload\ImageUpload`:
- добавлено публичное свойство `$showMetaDataBtn` булевого типа, котрое отвечает за отображения кнопки рендера формы заполнения метаданных;
- добавлено публичное свойство `$renderMetaDataFormUrl` - это урл к экшену, который будет рендерить форму для заполнения метаданных. Поумолчанию ведет на `/imagesUpload/meta-data/generate-form`, но можно указать свой кастомный.
2. Добавлена таблица в БД `{{%file_meta_data}}` с такой структурой:
```
id              INT         
file_id         INT         NOT NULL    fk
alt             STRING                  lang 
created_at      INT         NOT NULL
updated_at      INT         NOT NULL
```
3. В модуле `backend\modules\imagesUpload\ImagesUploadModule` добавлен контроллер `backend\modules\imagesUpload\controllers\MetaDataController` в котором находятся методы для рендера формы с метаданными и ее сохранения.
4. Для использования этого функционала нужно в форм конфиге для ImageUpload виджета указать `true` для значения свойства `showMetaDataBtn`:
```
'imagesUpload' => [
    'type'  => ActiveFormBuilder::INPUT_RAW,
    'value' => ImageUpload::widget([
        'model'                 => $this,
        'attribute'             => 'imagesUpload',
        'saveAttribute'         => static::PHOTO_ALBUM_IMAGES,
        'multiple'              => true,
        'showMetaDataBtn'       => true,
    ]),
]
```
Также можно указать путь к кастомному экшену генерации формы.