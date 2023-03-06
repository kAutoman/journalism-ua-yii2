Конструктор страниц
===================================

Подключение конструктора на backend
-----------------------------

```php
    /**
     * Temporary sign which used for saving images before model save
     *
     * @var string
     */
    public $sign; // Как оказалось не только для картинок полезен :)

    public $content; // Аттрибут для конструктора

    public function init()
    {
        parent::init();

        if (!$this->sign) {
            $this->sign = \Yii::$app->security->generateRandomString();
        }
    }

    public function behaviors()
    {
        return [
            // ---
            'content' => [
                'class' => BuilderBehavior::className(),
                'attributes' => ['content'], // Аттрибут(ы) для конструктора
                'widgets' => [
                    /** Можно ограничить виджеты для модели */
                ],
            ],
        ];
    }

    public function getFormConfig()
    {
        $main = [
            // ---
        ];

        $config = [
            'form-set' => [
                \help\bt('Content') => [
                    'content' => BuilderConfig::config($this, 'content'), // Подключение конструктора
                ],
                /** Или указать в таком виде, ограничив виджеты */
                \help\bt('Content') => [
                    'content' => BuilderConfig::config($this, 'content', [
                        'models' => [
                            'common\models\builder\StringBuilderModel',
                        ],
                    ]), // Подключение конструктора
                ],
                \help\bt('Main') => $main,
            ],
        ];

        return $config;
    }
```

Добавление нового елемента на backend
-----------------------------

Для начала создаем класс и наследуем его от [\common\components\BuilderModel](../common/components/BuilderModel.php).
Затем подключаем его в [backend/config/main.php](../backend/config/main.php), в настройках модуля builder

```php
'builder' => [
	'class' => 'backend\modules\builder\Module',
	'enablePreview' => false, // Показывать превью страницы
	'models' => [
		'common\models\builder\StringBuilderModel',
		'common\models\builder\TextBuilderModel',
		'common\models\builder\ImageBuilderModel',
		// Ваши модели
	],
],
```

Пример модели

```php
class ImageBuilderModel extends BuilderModel
{
    public $image;
    // Аттрибуты :)

    public function getName() // Имя елемента
    {
        return 'Image';
    }

    public function rules() // Рулсы (пока сомнительно работают)
    {
        return ArrayHelper::merge(parent::rules(), [
            [['image'], 'safe'],
        ]);
    }

    public function getAttributeLabels() // Заголовки аттрибутов
    {
        return [
            'image' => 'Image',
        ];
    }

    public function getConfig() // Аттрибуты заполняются как обычные аттрибуты в админке
    {
        return ArrayHelper::merge(parent::getDefaultConfig(), [
            'image' => [
                'type' => ActiveFormBuilder::INPUT_WIDGET,
                'widgetClass' => BuilderImageUpload::className(),
                'options' => [
                    'aspectRatio' => false, //Пропорция для кропа
                    'multiple' => false, //Вкл/выкл множественную загрузку
                ],
            ],
        ]);
    }

    public function getLocalized() // Аттрибуты с переводами
    {
        return [];
    }
}
```

Также нужно добавить отображение в предпросмотре [backend/modules/builder/widgets/previewWidget/PreviewWidget.php](../backend/modules/builder/widgets/previewWidget/PreviewWidget.php)

```php
class PreviewWidget extends Widget
{
    /** @var BuilderModel[] */
    public $widgets = [];

    public function run()
    {
        $renders = [];

        $renders[] = $this->render('default'); // Подключает ассетсы

        foreach ($this->widgets as $widget) {
            $widget->proccessFiles(); // Подключаем файлы
            $widget->proccessTranslation(); // Подключаем переводы

            switch ($widget::className()) {
                case StringBuilderModel::className():
                    $renders[] = $this->render('string', ['model' => $widget]);
                    break;
                case TextBuilderModel::className():
                    $renders[] = $this->render('text', ['model' => $widget]);
                    break;
                case ImageBuilderModel::className():
                    $renders[] = $this->render('image', ['model' => $widget]);
                    break;
                // Вот тут через кейсы все и добавляем
            }
        }

        return implode("\n", $renders);
    }
}

```

Подключение на frontend
-----------------------

Точнее в common, подключаем Параметр и бихевиор

```php
class Article extends ActiveRecord implements Translateable
{
    public $content = [];
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'content' => [
                'class' => CommonBuilderBehavior::className(),
                'attributes' => ['content'],
            ],
        ];
    }
}
```

Теперь в content лежит массив из [BuilderModel](../common/components/BuilderModel.php), файлы приходят в виде массива [File](https://github.com/vadimsemenykv/yii2-file-processor-module/blob/master/src/models/File.php)
