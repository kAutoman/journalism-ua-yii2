<?php

namespace backend\components\gii\builder;

use Yii;
use yii\gii\CodeFile;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\validators\RequiredValidator;

/**
 * Class Generator
 *
 * @package backend\components\gii\builder
 */
class Generator extends \yii\gii\Generator
{
    const TYPE_STRING = 1;
    const TYPE_TEXT = 2;
    const TYPE_EDITOR = 3;
    const TYPE_INTEGER = 4;
    const TYPE_BOOLEAN = 5;
    const TYPE_FLOAT = 6;
    const TYPE_FILE = 7;

    public $className;
    public $ns = 'common\models\builder';
    public $title = '';
    public $fields = [];
    public $viewFileName;

    public $template = 'advanced';
    public $baseClass = 'common\modules\builder\models\BuilderModel';

    public $generateLabelsFromComments = true;
    public $useTablePrefix = true;
    public $enableI18N = true;

    public $templates = ['default' => '@backend/components/gii/builder/default'];

    /**
     * @var array
     */
    public static $types = [
        self::TYPE_STRING => 'String',
        self::TYPE_TEXT => 'Text',
        self::TYPE_EDITOR => 'Editor',
        self::TYPE_INTEGER => 'Integer',
        self::TYPE_BOOLEAN => 'Boolean',
        self::TYPE_FLOAT => 'Float',
        self::TYPE_FILE => 'File/image',
    ];

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Builder Model Generator';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator generates Builder model class';
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates()
    {
        return ['model.php'];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['className', 'ns'], 'required'],
            [['className', 'ns', 'viewFileName', 'title'], 'string'],
            [['className'], 'match', 'pattern' => '/^\w+$/', 'message' => 'Only word characters are allowed.'],
            [['ns'], 'match', 'pattern' => '/^[\w\\\\]+$/', 'message' => 'Only word characters and backslashes are allowed.'],
            [['fields'], 'validateFields']
        ]);
    }

    /**
     * @return array
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'ns' => 'Model namespace'
        ]);
    }

    /**
     * @param $attribute
     */
    public function validateFields($attribute)
    {
        $requiredValidator = new RequiredValidator();

        foreach ($this->$attribute as $index => $row) {
            $error = null;
            foreach ($row as $key => $value) {
                if (in_array($key, ['name', 'type', 'label'])) {
                    if (!$requiredValidator->validate($value, $error)) {
                        $key = $attribute . '[' . $index . '][' . $key . ']';
                        $this->addError($key, 'Field is required');
                    }
                }
            }
        }
    }

    /**
     * Generates the code based on the current user input and the specified code template files.
     * This is the main method that child classes should implement.
     * Please refer to [[\yii\gii\generators\controller\Generator::generate()]] as an example
     * on how to implement this method.
     *
     * @return \yii\gii\CodeFile[]
     */
    public function generate()
    {
        $files = [];
        $params = [
            'className' => $this->className,
            'attributes' => $this->generateAttributes(),
            'labels' => $this->generateLabels(),
            'rules' => $this->generateRules(),
            'fileAttributes' => $this->getFileAttributes(),
            'attributeTypes' => $this->getAttributeTypesArray(),
            'title' => $this->enableI18N ? "Yii::t('{$this->messageCategory}', '{$this->title}')" : $this->title,
        ];

        $file = Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/' . $this->className . '.php';
        $files[] = new CodeFile($file, $this->render('model.php', $params));

        return $files;
    }

    public function getFormConfigType(int $type, $name)
    {
        switch ($type) {
            case self::TYPE_TEXT:
                return "['type' => FormBuilder::INPUT_TEXTAREA]";
            case self::TYPE_EDITOR:
                return "[
                'type' => FormBuilder::INPUT_WIDGET,
                'widgetClass' => Editor::class,
                'widgetOptions' => [
                    'model' => \$this,
                    'attribute' => '{$name}'
                ]
            ]";
            case self::TYPE_BOOLEAN:
                return "['type' => FormBuilder::INPUT_CHECKBOX]";
            case self::TYPE_FILE:
                return "[
                'type' => FormBuilder::INPUT_WIDGET,
                'widgetClass' => ImageUpload::class,
                'options' => [
                    'model' => \$this,
                    'attribute' => '{$name}',
                    'saveAttribute' => self::{$this->getFileAttributes()[$name]},
                    'allowedFileExtensions' => [],
                    'multiple' => false
                ]
            ]";
            default:
                return "['type' => FormBuilder::INPUT_TEXT]";
        }
    }

    /**
     * @return array
     */
    protected function generateAttributes()
    {
        $attributes = [];
        foreach ($this->fields as $field) {
            switch ((int) $field['type']) {
                case self::TYPE_INTEGER:
                case self::TYPE_FILE:
                    $attributes[$field['name']] = 'int';
                    break;
                case self::TYPE_BOOLEAN:
                    $attributes[$field['name']] = 'boolean';
                    break;
                case self::TYPE_FLOAT:
                    $attributes[$field['name']] = 'float';
                    break;
                default:
                    $attributes[$field['name']] = 'string';
                    break;
            }
        }

        return $attributes;
    }

    /**
     * @return array
     */
    protected function generateAttributeTypes()
    {
        $attributes = [];
        foreach ($this->fields as $field) {
            switch ((int) $field['type']) {
                case self::TYPE_INTEGER:
                case self::TYPE_FILE:
                    $attributes[$field['name']] = 'int';
                    break;
                case self::TYPE_BOOLEAN:
                    $attributes[$field['name']] = 'boolean';
                    break;
                case self::TYPE_FLOAT:
                    $attributes[$field['name']] = 'float';
                    break;
                default:
                    $attributes[$field['name']] = 'string';
                    break;
            }
        }

        return $attributes;
    }

    /**
     * Generates array of labels for the model.
     *
     * @return array
     */
    protected function generateLabels()
    {
        $labels = [];
        foreach ($this->fields as $field) {
            $labels[$field['name']] = $this->enableI18N ? "Yii::t('{$this->messageCategory}', '{$field['label']}')" : $field['label'];
        }

        return $labels;
    }

    /**
     * Generates array of types for the model.
     *
     * @return array
     */
    protected function getAttributeTypesArray()
    {
        $types = [];
        foreach ($this->fields as $field) {
            $types[$field['name']] = (int) $field['type'];
        }

        return $types;
    }

    /**
     * Generates validation rules for the model.
     *
     * @return array the generated validation rules
     */
    protected function generateRules()
    {
        $types = [];
        foreach ($this->fields as $field) {
            $attribute = $field['name'];
            if ((bool) $field['isRequired']) {
                $types['required'][] = $attribute;
            }
            switch ((int) $field['type']) {
                case self::TYPE_INTEGER:
                    $types['integer'][] = $attribute;
                    break;
                case self::TYPE_BOOLEAN:
                    $types['boolean'][] = $attribute;
                    break;
                case self::TYPE_FLOAT:
                    $types['number'][] = $attribute;
                    break;
                case self::TYPE_STRING:
                case self::TYPE_TEXT:
                case self::TYPE_EDITOR:
                    $types['string'][] = $attribute;
                    break;
                default:
                    $types['safe'][] = $attribute;
                    break;
            }
        }

        $rules = [];
        foreach ($types as $type => $columns) {
            $rules[] = "[['" . implode("', '", $columns) . "'], '$type']";
        }

        return $rules;
    }

    /**
     * @return array
     */
    protected function getFileAttributes()
    {
        $attributes = [];
        foreach ($this->fields as $field) {
            if ((int) $field['type'] === self::TYPE_FILE) {
                $attributes[$field['name']] = $this->createConstant('Builder' . $this->className . $field['label']);
            }
        }

        return $attributes;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    private function createConstant($string)
    {
        $string = Inflector::camelize($string);
        $string = Inflector::underscore($string);

        return strtoupper($string);
    }
}
