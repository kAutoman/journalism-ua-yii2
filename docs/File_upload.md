##1.Загрузка файлов(одиночная)

поле в таблице
```php
'file_id' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL COMMENT "File"'
```

поле в форме:

```php
'file_id' => [
    'type' => ActiveFormBuilder::INPUT_FILE,
],
```

бихевиор в моделе в common/models/base

```php
'file' => [
    'class' => \metalguardian\fileProcessor\behaviors\UploadBehavior::className(),
    'attribute' => 'file_id',
    //'required' => false,
],
```

Можно настроить автоматическое удаление файлов, после удаления модели

```php
'delete_file' => [
    'class' => \metalguardian\fileProcessor\behaviors\DeleteBehavior::className(),
    'attribute' => 'file_id',
],
```

Или совмещенный бихевиор как для загрузки, так и удаления

```php
'file' => [
    'class' => \metalguardian\fileProcessor\behaviors\UploadDeleteBehavior::className(),
    'attribute' => 'file_id',
],
```

поле file_id необходимо убрать из rules модели (для полей с именем image_id, file_id - gii делает это автоматически)

Ручная загрузка
---------------------

если необходимо какие то сложные операции с файлами, или необходима мультизагрузка (имя поля должно быть 'file[]'), можно загружать так:

для примера мультизагрузка:

```php
$this->images = UploadedFile::getInstances($this, 'images');

foreach ($this->images as $image) {
    $model = new ImageModel();
    $model->image_id = FPM::transfer()->saveUploadedFile($image);
    $model->save();
}
```


##2.Загрузка файлов(множественная)

Если при генерации CRUD вы поставили галочку Is Image, то в код модели будет добавлен
небходимый код для фунционирования виджета, останется только прописать константу в EntityToFile
Если таких виджетов нужно больше одного, то достаточно в моделе создать дополнительные публичные переменные для каждого нового виджета,
в attributeLabels() прописать их названия, добавить виджеты в getFormConfig() и не забыть создать для них константы в EntityToFile.
Например, в back-моделе нужно прописать EntityToFile::TYPE_ARTICLE_GALLERY_IMAGES

```php
'galleryImages' => [
    'type' => ActiveFormBuilder::INPUT_RAW,
    'value' => ImageUpload::widget([
        'model' => $this,
        'attribute' => 'galleryImages',
        'saveAttribute' => EntityToFile::TYPE_ARTICLE_GALLERY_IMAGES,<-------
        'multiple' => true,
        'aspectRatio' => 300/200
    ])
],
```

а в базовой моделе EntityToFile определить эту константу, например
```php
abstract class EntityToFile extends \common\components\model\ActiveRecord
{
    const TYPE_ARTICLE_GALLERY_IMAGES = 'article_gallery_images';
    const TYPE_ARTICLE_TITLE_IMAGE = 'article_title_image';
}
```
Это нужно для идентификации типа файла в таблице entity_to_file, если для одной модели есть несколько типов файлов,
например, titleImage и galleryImage.

По умолчанию для загрузки доступны любые типы файлов. Если нужно указать допустимые - это делается через параметр allowedFileExtensions:
```php
'someFiles' => [
    'type' => ActiveFormBuilder::INPUT_RAW,
    'value' => ImageUpload::widget([
        'model' => $this,
        'attribute' => 'someFiles',
        'saveAttribute' => EntityToFile::TYPE_MIXED_FILES,
        'multiple' => true,
        'allowedFileExtensions' => ['mp4', 'pdf', 'jpg'], // <-- список расширений, без точек 
        'aspectRatio' => 300/200
    ])
],
```
Данный список расширений идет параметром 'accept' в `<input type='file' accept='XXX' />`, а так же в JS валидацию виджета. 
Так же, пареметр aspectRatio можно указывать и он будет работать только для картинок которые можно кропать (см. backend\modules\imagesUpload\widgets\imagesUpload\ImageUpload::getCropableImagesExtensions()).
Для любых других типов файлов кнопка кропа не отображается.

##3.Отображение изображений(ресайзнутых) на фронте


в common конфиге в модуле fileProcessor добавляются размеры изображений для ресайза

```php
'modules' => [
    'fileProcessor' => [
        'class' => '\metalguardian\fileProcessor\Module',
        'imageSections' => [
            'profile' => [
                'view' => [
                    'action' => \metalguardian\fileProcessor\helpers\FPM::ACTION_ADAPTIVE_THUMBNAIL,
                    'width' => 120,
                    'height' => 120,
                ],
            ],
        ],
    ],
],
```

во вьюхах вызывать так:

```php
<?= \metalguardian\fileProcessor\helpers\FPM::image($model->image_id, 'profile', 'view') ?>
```
