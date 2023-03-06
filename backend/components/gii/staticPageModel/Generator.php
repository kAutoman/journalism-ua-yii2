<?php
/**
 * Created by anatolii
 */

namespace backend\components\gii\staticPageModel;

use common\models\Configuration;
use Yii;
use yii\db\Schema;
use yii\db\TableSchema;
use yii\gii\CodeFile;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\base\NotSupportedException;
use yii\helpers\VarDumper;
use yii\validators\RequiredValidator;

/**
 * This generator generates model for static pages
 */
class Generator extends \yii\gii\Generator
{
    public $moduleId;
    public $ns;
    public $modelClassName;
    public $controllerClass;
    public $title;
    public $keys = [];
    public $isSeo = false;
    public $relationsForRelatedFormWidget = [];
    public $messageCategory = 'back/';

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Static Page Model Generator';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator generates model for static pages';
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates()
    {
        return ['model.php', 'controller.php', 'common_model.php'];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['moduleId', 'ns', 'modelClassName', 'controllerClass', 'title'], 'required'],
            [['keys'], 'validateKeys'],
            [['ns', 'modelClassName', 'title'], 'filter', 'filter' => 'trim'],
            [['ns'], 'filter', 'filter' => function ($value) { return trim($value, '\\'); }],
            [['ns', 'controllerClass'], 'match', 'pattern' => '/^[\w\\\\]+$/', 'message' => 'Only word characters and backslashes are allowed.'],
            [['ns'], 'validateNamespace'],
            [['modelClassName', 'moduleId'], 'match', 'pattern' => '/^\w+$/', 'message' => 'Only word characters are allowed.'],
            [['title'], 'string'],
            [['isSeo', 'enableI18N'], 'boolean'],
            [['messageCategory'], 'validateMessageCategory', 'skipOnEmpty' => false],
            [['relationsForRelatedFormWidget'], 'safe'],
        ];
    }

    /**
     * @param $attribute
     */
    public function validateKeys($attribute)
    {
        $requiredValidator = new RequiredValidator();

        foreach ($this->$attribute as $index => $row) {
            $error = null;
            foreach ($row as $key => $value) {
                if (!in_array($key, ['hint', 'rule'])) {
                    $requiredValidator->validate($value, $error);
                    if (!empty($error)) {
                        $key = $attribute . '[' . $index . '][' . $key . ']';
                        $this->addError($key, $error);
                    }
                }
            }
        }
    }

    /**
     * Validates the namespace.
     *
     * @param string $attribute Namespace variable.
     */
    public function validateNamespace($attribute)
    {
        $value = $this->$attribute;
        $value = ltrim($value, '\\');
        $path = Yii::getAlias('@' . str_replace('\\', '/', $value), false);
        if ($path === false) {
            $this->addError($attribute, 'Namespace must be associated with an existing directory.');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'ns' => 'Model namespace',
            'isSeo' => 'Add seo behavior',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function hints()
    {
        return array_merge(parent::hints(), [

        ]);
    }

    /**
     * @inheritdoc
     */
    public function stickyAttributes()
    {
        return ['moduleId'];
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        if ($this->relationsForRelatedFormWidget === '') {
            $this->relationsForRelatedFormWidget = [];
        }
        $controllerFile = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->controllerClass, '\\')) . '.php');
        $files = [
            new CodeFile($controllerFile, $this->render('controller.php')),
        ];

        $params = [
            'behaviors' => $this->generateBehaviors(),
            'hasFiles' => $this->checkFileTypeExist()
        ];
        $files[] = new CodeFile(
            Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/' . $this->modelClassName . '.php',
            $this->render('model.php', $params)
        );
        $files[] = new CodeFile(
            Yii::getAlias('@common/models') . '/' . $this->modelClassName . '.php',
            $this->render('common_model.php', $params)
        );

        return $files;
    }

    /**
     * @param \yii\db\TableSchema $table the table schema
     * @return array generated behaviors
     */
    public function generateBehaviors()
    {
        $behaviors = [];
        if ($this->isSeo) {
            $code = "'seo' => [
                'class' => \\notgosu\\yii2\\modules\\metaTag\\components\\MetaTagBehavior::className(),
            ]";
            $behaviors[] = $code;
        }


        return $behaviors;
    }

    /**
     * @param int $constantValue
     *
     * @return null|string
     * @throws \ReflectionException
     */
    public function getConstantName($constantValue)
    {
        $configurationClass = new \ReflectionClass(Configuration::class);
        $constants = $configurationClass->getConstants();
        foreach ($constants as $name => $value) {
            if (substr_count($name, 'TYPE_') && (int) $constantValue === $value) {
                return $name;
            }
        }

        return null;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function generateKeyName($key)
    {
        $key = $this->modelClassName . '_' . $key;

        return $this->camelCase($key);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function formatToConstant($string)
    {
        $string = $this->camelCase($string);
        $string = $this->camelcaseToUnderscore($string);

        return strtoupper($string);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function camelcaseToUnderscore($string)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $string, $matches);
        $return = $matches[0];
        foreach ($return as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }

        return implode('_', $return);
    }

    /**
     * @param string $str
     * @param array $noStrip
     *
     * @return string
     */
    public function camelCase($str, $noStrip = [])
    {
        // non-alpha and non-numeric characters become spaces
        $str = preg_replace('/[^a-z0-9' . implode("", $noStrip) . ']+/i', ' ', $str);
        $str = trim($str);
        // uppercase the first character of each word
        $str = ucwords($str);
        $str = str_replace(" ", "", $str);
        $str = lcfirst($str);

        return $str;
    }

    /**
     * Generates code for active field
     *
     * @param array $key
     * @return string
     */
    public function generateFormFieldConfig(array $key)
    {
        switch ($key['type']) {
            case Configuration::TYPE_TEXT:
            case Configuration::TYPE_HTML:
                return "[
                    'type' => FormBuilder::INPUT_TEXTAREA
                ]";
            case Configuration::TYPE_BOOLEAN:
                return "[
                    'type' => FormBuilder::INPUT_CHECKBOX
                ]";
            case Configuration::TYPE_FILE:
                $label = '$this->attributeLabels()';
                $model = '$this';
                $attribute = $this->camelCase($key['id']);
                $saveAttribute = $this->formatToConstant($key['id']);
                return "[
                    'type' => FormBuilder::INPUT_RAW,
                    'label' => {$label}['image'] ?? null,
                    'value' => ImageUpload::widget([
                        'attribute' => '{$attribute}',
                        'model' => {$model},
                        'saveAttribute' => self::{$saveAttribute}
                    ])
                ]";
            default:
                return "[
                    'type' => FormBuilder::INPUT_TEXT
                ]";
        }
    }

    /**
     * Generates validation rules for the model.
     *
     * @return array the generated validation rules
     */
    public function generateRules()
    {
        $types = [];
        foreach ($this->keys as $key) {
            $attribute = $this->camelCase($key['id']);

            if ((bool) $key['isRequired']) {
                $types['required'][] = $attribute;
            }

            switch ($key['type']) {
                case Configuration::TYPE_INTEGER:
                    $types['integer'][] = $attribute;
                    break;
                case Configuration::TYPE_BOOLEAN:
                    $types['boolean'][] = $attribute;
                    break;
                case Configuration::TYPE_DOUBLE:
                    $types['number'][] = $attribute;
                    break;
                case Configuration::TYPE_STRING:
                case Configuration::TYPE_TEXT:
                case Configuration::TYPE_HTML:
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
     * @param $type
     *
     * @return string
     */
    public function getPropertyType($type)
    {
        switch ($type) {
            case Configuration::TYPE_INTEGER:
                return "integer";
            case Configuration::TYPE_DOUBLE:
                return "float";
            case Configuration::TYPE_BOOLEAN:
                return "boolean";
            default:
                return "string";
        }
    }

    /**
     * @return bool
     */
    private function checkFileTypeExist(): bool
    {
        $hasFiles = false;
        foreach ($this->keys as $key) {
            if ((int) $key['type'] === Configuration::TYPE_FILE) {
                $hasFiles = true;
            }
        }

        return $hasFiles;
    }
}
