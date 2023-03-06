<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\components\gii\crud;

use Yii;
use yii\db\Schema;
use yii\db\TableSchema;
use yii\gii\CodeFile;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\base\NotSupportedException;
use yii\helpers\VarDumper;

/**
 * This generator will generate one or multiple ActiveRecord classes for the specified database table.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Generator extends \yii\gii\generators\model\Generator
{
    public $template = 'advanced';
    public $ns = 'backend\modules\\';
    public $baseClass = 'common\components\model\ActiveRecord';
    public $controllerClass;
    public $baseControllerClass = '\backend\components\BackendController';
    public $isSeo = false;
    public $generateLabelsFromComments = true;
    public $useTablePrefix = true;
    public $enableI18N = true;
    public $hideExistingBaseModel = true;
    public $relationsForRelatedFormWidget = [];
    public $imageUploaders = [];

    protected $tableSchema = false;

    protected $hasLangTable = null;

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Advanced CRUD Generator';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator generates an ActiveRecord class for the specified database table.
            Added generation multi language model, language model, other improvements';
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates()
    {
        return ['controller.php', 'search.php', 'full.php'];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['isSeo'], 'boolean'],
            [['controllerClass', 'baseControllerClass', 'relationsForRelatedFormWidget', 'imageUploaders'], 'safe']
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'hideExistingBaseModel' => 'Hide existing base models',
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
            'hideExistingBaseModel' => 'Do not propose to generate existing base models files',
        ]);
    }


    /**
     * @inheritdoc
     */
    public function generate()
    {
        $controllerFile = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->controllerClass, '\\')) . '.php');
        $files = [
            new CodeFile($controllerFile, $this->render('controller.php')),
        ];

        $relations = $this->generateRelations();
        $db = $this->getDbConnection();
        foreach ($this->getTableNames() as $tableName) {
            $className = $this->generateClassName($tableName);
            $tableSchema = $db->getTableSchema($tableName);
            $translationTableSchema = $db->getTableSchema($tableName . '_translation');
            $params = [
                'tableName' => $tableName,
                'className' => $className,
                'tableSchema' => $tableSchema,
                'labels' => $this->generateLabels($tableSchema),
                'rules' => $this->generateRules($tableSchema),
                'relations' => isset($relations[$tableName]) ? $this->checkMultiLangRelation($className, $relations[$tableName]) : [],
                'multiLanguageModel' => $this->isMultiLanguageTable($tableSchema),
                'behaviors' => $this->generateBehaviors($tableSchema),
                'translationAttributes' => $this->isMultiLanguageTable($tableSchema) ? $this->getTranslationAttributes($tableSchema, $translationTableSchema) : [],
                'viewColumns' => $this->getViewColumns(),
                'indexColumns' => $this->getIndexColumns(),
                'formColumns' => $this->getFormColumns(),
            ];

            $files[] = new CodeFile(
                Yii::getAlias('@common/models') . '/' . $this->modelClass . '.php',
                $this->render('common_model.php', $params)
            );

            $files[] = new CodeFile(
                Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/' . $className . '.php',
                $this->render('full.php', $params)
            );

            if ($this->isMultiLanguageTable($tableSchema)) {
                $translateParams = [
                    'tableName' => $tableName . '_translation',
                    'className' => $className . 'Translation',
                    'tableSchema' => $translationTableSchema,
                    'labels' => $this->generateLabels($translationTableSchema),
                    'rules' => $this->generateRules($translationTableSchema),
                ];
                $files[] = new CodeFile(
                    Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/' . $className . 'Translation.php',
                    $this->render('translation.php', $translateParams)
                );
            }

            $searchModel = Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/' . $className . 'Search.php';
            $files[] = new CodeFile(
                $searchModel,
                $this->render(
                    'search.php',
                    [
                        'rules' => $this->generateSearchRules(),
                        'searchConditions' => $this->generateSearchConditions(),
                        'hasLangTable' => $this->isMultiLanguageTable($tableSchema),
                    ]
                )
            );
        }

        return $files;
    }

    /**
     * @inheritdoc
     */
    public function generateLabels($table)
    {
        $labels = [];
        foreach ($table->columns as $column) {
            if ($this->checkNoLabelAttribute($table, $column->name)) {
                continue;
            }
            if ($this->generateLabelsFromComments && !empty($column->comment)) {
                $labels[$column->name] = $column->comment;
            } elseif (!strcasecmp($column->name, 'id')) {
                $labels[$column->name] = 'ID';
            } else {
                $label = Inflector::camel2words($column->name);
                if (!empty($label) && substr_compare($label, ' id', -3, 3, true) === 0) {
                    $label = substr($label, 0, -3) . ' ID';
                }
                $labels[$column->name] = $label;
            }
        }

        return $labels;
    }

    /**
     * remove default fields from attribute labels
     *
     * @param TableSchema $table
     * @param $attribute
     * @return array
     */
    public function checkNoLabelAttribute($table, $attribute)
    {
        $attributes = [
            'created_at',
            'updated_at',
        ];
        if ($this->isTranslationTable($table)) {
            $attributes = [
                'model_id',
                'language',
            ];
        }

        return in_array($attribute, $attributes, true);
    }

    /**
     * @return bool
     */
    public function hasLangTable()
    {
        if ($this->hasLangTable === null) {
            foreach ($this->fields as $field) {
                if (isset($field['isLang']) && $field['isLang']) {
                    return $this->hasLangTable = true;
                }
            }
        }

        return $this->hasLangTable;
    }

    /**
     * @inheritdoc
     */
    public function generateRules($table)
    {
        $types = [];
        $lengths = [];
        $other = [];
        $foreignKeys = $this->getForeignKeys($table);
        foreach ($table->columns as $column) {
            if ($column->autoIncrement || $this->checkNoRuleAttribute($table, $column->name)) {
                continue;
            }
            if (!$column->allowNull && $column->defaultValue === null) {
                $types['required'][] = $column->name;
            }
            switch ($column->type) {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                    $types['integer'][] = $column->name;
                    break;
                case Schema::TYPE_BOOLEAN:
                    $types['boolean'][] = $column->name;
                    break;
                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DOUBLE:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                    $types['number'][] = $column->name;
                    break;
                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                    if ($column->defaultValue === null && $column->allowNull === true) {
                        $other[] = "[['" . $column->name . "'], 'default', 'value' => " . VarDumper::export($column->defaultValue) . "]";
                    }
                    $types['date'][] = $column->name;
                    break;
                case Schema::TYPE_TIMESTAMP:
                    $types['safe'][] = $column->name;
                    break;
                default: // strings
                    if ($column->size > 0) {
                        $lengths[$column->size][] = $column->name;
                    } else {
                        $types['string'][] = $column->name;
                    }
            }
            if ($column->defaultValue !== null && $column->allowNull === false) {
                $other[] = "[['" . $column->name . "'], 'default', 'value' => " . VarDumper::export($column->defaultValue) . "]";
            }
            if (in_array($column->name, ['url', 'link'], true)) {
                $other[] = "[['" . $column->name . "'], 'url', 'defaultScheme' => 'http']";
            }
            if (in_array($column->name, $foreignKeys, true)) {
                $foreignKeysTables = $this->getForeignKeysTables($table);
                $relTableName = isset($foreignKeysTables[$column->name]) ? $foreignKeysTables[$column->name] : null;
                if ($relTableName) {
                    $relClassName = '\\common\\models\\' . $this->generateClassName($relTableName);
                    // TODO: fix 'id' attribute to real
                    $other[] = "[['" . $column->name . "'], 'exist', 'targetClass' => " . $relClassName . "::className(), 'targetAttribute' => 'id']";
                }
            }
        }
        $rules = [];
        foreach ($types as $type => $columns) {
            $rules[] = "[['" . implode("', '", $columns) . "'], '$type']";
        }
        foreach ($lengths as $length => $columns) {
            $rules[] = "[['" . implode("', '", $columns) . "'], 'string', 'max' => $length]";
        }
        $rules = ArrayHelper::merge($rules, $other);

        // Unique indexes rules
        try {
            $db = $this->getDbConnection();
            $uniqueIndexes = $db->getSchema()->findUniqueIndexes($table);
            foreach ($uniqueIndexes as $uniqueColumns) {
                // Avoid validating auto incremental columns
                if (!$this->isColumnAutoIncremental($table, $uniqueColumns)) {
                    $attributesCount = count($uniqueColumns);

                    if ($attributesCount == 1) {
                        $rules[] = "[['" . $uniqueColumns[0] . "'], 'unique']";
                    } elseif ($attributesCount > 1) {
                        $labels = array_intersect_key($this->generateLabels($table), array_flip($uniqueColumns));
                        $lastLabel = array_pop($labels);
                        $columnsList = implode("', '", $uniqueColumns);
                        $rules[] = "[['" . $columnsList . "'], 'unique', 'targetAttribute' => ['" . $columnsList . "'], 'message' => 'The combination of " . implode(', ', $labels) . " and " . $lastLabel . " has already been taken.']";
                    }
                }
            }
        } catch (NotSupportedException $e) {
            // doesn't support unique indexes information...do nothing
        }

        return $rules;
    }

    /**
     * remove default fields from rule labels
     *
     * @param TableSchema $table
     * @param $attribute
     * @return array
     */
    public function checkNoRuleAttribute($table, $attribute)
    {
        $attributes = [
            'created_at',
            'updated_at',
            'image_id',
        ];

        if ($this->isTranslationTable($table)) {
            $attributes = [
                'model_id',
                'language',
            ];
        }

        return in_array($attribute, $attributes, true);
    }

    /**
     * @param \yii\db\TableSchema $table the table schema
     * @return array generated behaviors
     */
    public function generateBehaviors($table)
    {
        $behaviors = [];
        if ($this->isMultiLanguageTable($table)) {
            $behaviors[] = "'translateable' => [
                'class' => \\creocoder\\translateable\\TranslateableBehavior::className(),
                'translationAttributes' => static::getTranslationAttributes(),
            ]";
        }
        $timestamp = [];
        foreach ($table->columns as $column) {
            if (in_array($column->name, ['created_at', 'updated_at'], true)) {
                $timestamp[] = $column->name;
            }
        }
        if (is_array($timestamp) && !empty($timestamp)) {
            $code = "'timestamp' => [
                'class' => \\yii\\behaviors\\TimestampBehavior::className(),";
            if (!in_array('created_at', $timestamp, true)) {
                $code .= "
                'createdAtAttribute' => false,";
            }
            if (!in_array('updated_at', $timestamp, true)) {
                $code .= "
                'updatedAtAttribute' => false,";
            }
            $code .= "
            ]";
            $behaviors[] = $code;
        }

        if ($this->isSeo) {
            $code = "'seo' => [
                'class' => \\notgosu\\yii2\\modules\\metaTag\\components\\MetaTagBehavior::className(),
            ]";
            $behaviors[] = $code;
        }


        return $behaviors;
    }

    /**
     * @param $class
     * @param array $relations
     * @return array
     */
    public function checkMultiLangRelation($class, array $relations)
    {
        $newRelations = [];
        foreach ($relations as $name => $relation) {
            if ($relation[1] === $class . 'Translation') {
                $newRelations['Translations'] = $relation;
            } else {
                $newRelations[$name] = $relation;
            }
        }

        return $newRelations;
    }

    /**
     * @param \yii\db\TableSchema $table the table schema
     * @param \yii\db\TableSchema $translationTable the table schema
     * @return array
     */
    public function getTranslationAttributes($table, $translationTable)
    {
        if (!$translationTable) {
            return [];
        }
        $attributes = [];
        foreach ($translationTable->columns as $column) {
            if (in_array($column->name, $table->getColumnNames(), true)) {
                $attributes[] = $column->name;
            }
        }

        return $attributes;
    }

    /**
     * @param TableSchema $table
     * @return bool
     */
    public function isMultiLanguageTable($table)
    {
        $db = $this->getDbConnection();
        return (boolean)$db->getTableSchema($table->name . '_translation');
    }

    /**
     * @param TableSchema $table
     * @return bool
     */
    public function isTranslationTable($table)
    {
        $db = $this->getDbConnection();
        $baseTableName = preg_replace('/'. preg_quote('_translation', '/') . '$/', '', $table->name);
        return $this->endsWith($table->name, '_translation') && $db->getTableSchema($baseTableName);
    }

    /**
     * @param $haystack
     * @param $needle
     * @return bool
     */
    public function endsWith($haystack, $needle) {
        $haystackLen = strlen($haystack);
        $needleLen = strlen($needle);
        if ($needleLen > $haystackLen) {
            return false;
        }
        return substr_compare($haystack, $needle, $haystackLen - $needleLen, $needleLen) === 0;
    }

    /**
     * @param $tableSchema
     * @param string $label
     * @param array $placeholders
     * @return string
     */
    public function generateStringWithTable($tableSchema, $label = '', $placeholders = [])
    {
        $string = $this->generateString($label, $placeholders);

        if ($this->isTranslationTable($tableSchema)) {
            $string .= " . ' [' . \$this->language . ']'";
        }

        return $string;
    }

    /**
     * Returns table schema for current model class or false if it is not an active record
     * @return boolean|\yii\db\TableSchema
     */
    public function getTableSchema()
    {
        if (!$this->tableSchema && isset($this->getTableNames()[0])) {
            $db = $this->getDbConnection();
            $tableName = $this->getTableNames()[0];
            $this->tableSchema = $db->getTableSchema($tableName, true);
        }

        return $this->tableSchema;

    }

    /**
     * @return array model column names
     */
    public function getColumnNames()
    {
        $schema = $this->getTableSchema();
        
        if ($schema) {
            return $schema->getColumnNames();
        }
        
        return false;
    }

    /**
     * @param $attribute
     * @return bool
     */
    public function checkNoViewAttribute($attribute)
    {
        static $attributes = [
            'created_at',
            'updated_at',
        ];

        return in_array($attribute, $attributes, true);
    }

    /**
     * @inheritdoc
     */
    public function generateColumnFormat($column)
    {
        $format = 'text';

        switch (true) {
            case stripos($column->name, 'published') !== false:
            case $column->phpType === 'boolean' || ($column->type === Schema::TYPE_SMALLINT && $column->size === 1):
                $format = 'boolean';
                break;
            case stripos($column->name, 'file') !== false:
                $format = 'file';
                break;
            case stripos($column->name, 'link') !== false:
            case stripos($column->name, 'url') !== false:
                $format = 'url';
                break;
            case $column->type === 'text':
                $format = 'ntext';
                break;
            case stripos($column->name, 'time') !== false && $column->phpType === 'integer':
                $format = 'datetime';
                break;
            case stripos($column->name, 'email') !== false:
                $format = 'email';
                break;
        }

        return $format;
    }

    /**
     * @param $attribute
     * @return bool
     */
    public function checkNoIndexAttribute($attribute)
    {
        static $attributes = [
            'created_at',
            'updated_at',
        ];

        return in_array($attribute, $attributes, true);
    }

    /**
     * @param $attribute
     * @return bool
     */
    public function checkNoSearchAttribute($attribute)
    {
        static $attributes = [
            'created_at',
            'updated_at',
        ];

        return in_array($attribute, $attributes, true);
    }

    /**
     * Generates code for active field
     * @param string $attribute
     * @return string
     */
    public function generateFormFieldConfig($attribute)
    {
        $tableSchema = $this->getTableSchema();
        if ($tableSchema === false || !isset($tableSchema->columns[$attribute])) {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $attribute)) {
                return "[
                'type' => ActiveFormBuilder::INPUT_PASSWORD,
            ]";
            } else {
                return "[
                'type' => ActiveFormBuilder::INPUT_TEXT,
            ]";
            }
        }
        $column = $tableSchema->columns[$attribute];
        $foreignKeys = $this->getForeignKeys($tableSchema);
        if ($attribute === 'published') {
            return "[
                'type' => ActiveFormBuilder::INPUT_CHECKBOX,
            ]";
        } elseif ($column->phpType === 'boolean' || ($column->type === Schema::TYPE_SMALLINT && $column->size === 1)) {
            return "[
                'type' => ActiveFormBuilder::INPUT_CHECKBOX,
            ]";
        } elseif (stripos($column->name, 'file') !== false) {
            return "[
                'type' => ActiveFormBuilder::INPUT_FILE,
            ]";
        } elseif ($column->type === Schema::TYPE_DATE) {
            return "[
                'type' => ActiveFormBuilder::INPUT_WIDGET,
                'widgetClass' => \\metalguardian\\dateTimePicker\\Widget::className(),
                'options' => [
                    'mode' => \\metalguardian\\dateTimePicker\\Widget::MODE_DATE,
                    'options' => [
                        'class' => 'form-control',
                    ],
                ],
            ]";
        } elseif ($column->type === Schema::TYPE_TIME) {
            return "[
                'type' => ActiveFormBuilder::INPUT_WIDGET,
                'widgetClass' => \\metalguardian\\dateTimePicker\\Widget::className(),
                'options' => [
                    'mode' => \\metalguardian\\dateTimePicker\\Widget::MODE_TIME,
                    'options' => [
                        'class' => 'form-control',
                    ],
                ],
            ]";
        } elseif ($column->type === Schema::TYPE_DATETIME) {
            return "[
                'type' => ActiveFormBuilder::INPUT_WIDGET,
                'widgetClass' => \\metalguardian\\dateTimePicker\\Widget::className(),
                'options' => [
                    'options' => [
                        'class' => 'form-control',
                    ],
                ],
            ]";
        } elseif ($column->type === 'text') {
            return "[
                'type' => ActiveFormBuilder::INPUT_WIDGET,
                'widgetClass' => \\backend\\components\\ImperaviContent::className(),
                'options' => [
                    'model' => \$this,
                    'attribute' => '$attribute',
                ]
            ]";
        } elseif (in_array($column->name, $foreignKeys, true)) {
            $foreignKeysTables = $this->getForeignKeysTables($tableSchema);
            $relTableName = isset($foreignKeysTables[$column->name]) ? $foreignKeysTables[$column->name] : null;
            if ($relTableName) {
                $relClassName = '\\common\\models\\' . $this->generateClassName($relTableName);
                return "[
                'type' => ActiveFormBuilder::INPUT_DROPDOWN_LIST,
                'items' => {$relClassName}::getItems(),
                'options' => [
                    'prompt' => '',
                ],
            ]";
            }
        } else {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $column->name)) {
                $input = 'INPUT_PASSWORD';
            } else {
                $input = 'INPUT_TEXT';
            }
            if (is_array($column->enumValues) && count($column->enumValues) > 0) {
                $dropDownOptions = [];
                foreach ($column->enumValues as $enumValue) {
                    $dropDownOptions[$enumValue] = Inflector::humanize($enumValue);
                }
                return "[
                'type' => ActiveFormBuilder::INPUT_DROPDOWN_LIST,
                'items' => " . preg_replace("/\n\s*/", ' ', VarDumper::export($dropDownOptions)) . ",
                'options' => [
                    'prompt' => '',
                ],
            ]";
            } elseif ($column->phpType !== 'string' || $column->size === null) {
                return "[
                'type' => ActiveFormBuilder::{$input},
            ]";
            } else {
                $class = '';
                if ($column->name == 'label' && isset($tableSchema->columns['alias'])) {
                    $class = '\'class\' => $this->isNewRecord ? \'s_name form-control\' : \'form-control\'';
                } elseif ($column->name == 'alias' && isset($tableSchema->columns['label'])) {
                    $class = '\'class\' => $this->isNewRecord ? \'s_alias form-control\' : \'form-control\'';
                }
                return "[
                'type' => ActiveFormBuilder::{$input},
                'options' => [
                    'maxlength' => true,
                    $class
                ],
            ]";
            }
        }
    }
    
    /**
     * @return array
     */
    public function getViewColumns()
    {
        $columns = [];
        if (($tableSchema = $this->getTableSchema()) === false) {
            foreach ($this->getColumnNames() as $name) {
                $columns[] = "'". $name . "'";
            }
        } else {
            foreach ($this->getTableSchema()->columns as $column) {
                if ($this->checkNoViewAttribute($column->name)) {
                    continue;
                }
                $format = $this->generateColumnFormat($column);
                if ($format == 'ntext') {
                    $columns[] = "[
                        'attribute' => '$column->name',
                        'format' => 'html',
                    ]";
                } else {
                    $columns[] = "'" . $column->name . ($format === 'text' ? "" : ":" . $format) . "'";
                }
            }
        }

        return $columns;
    }

    /**
     * @return array
     */
    public function getIndexColumns()
    {
        $count = 0;
        $columns = [];
        if (($tableSchema = $this->getTableSchema()) === false) {
            foreach ($this->getColumnNames() as $name) {
                if (++$count < 6) {
                    $columns[] = "'". $name . "'";
                } else {
                    $columns[] = "// '". $name . "'";
                }
            }
        } else {
            foreach ($tableSchema->columns as $column) {
                if ($this->checkNoIndexAttribute($column->name)) {
                    continue;
                }
                $format = $this->generateColumnFormat($column);
                $formatedColumn = $column->name . ($format === 'text' ? "" : ":" . $format) . "'";
                if (($count < 6 && $format !== 'ntext' && $column->name != 'id') || in_array($column->name, ['published', 'position'])) {
                    $columns[] = "'" . $formatedColumn;
                    $count++;
                } else {
                    $columns[] = "// '" . $formatedColumn;
                }
            }
        }

        return $columns;
    }

    /**
     * @return array
     */
    public function getFormColumns()
    {
        $columns = [];
        foreach ($this->getColumnNames() as $attribute) {
            if (!in_array($attribute, ['id', 'created_at', 'updated_at', 'modified_at', 'created', 'updated', 'modified'])) {
            $columns[$attribute] = $this->generateFormFieldConfig($attribute);
            }
        }

        return $columns;
    }

    /**
     * Generates validation rules for the search model.
     * @return array the generated validation rules
     */
    public function generateSearchRules()
    {
        if (($table = $this->getTableSchema()) === false) {
            return ["[['" . implode("', '", $this->getColumnNames()) . "'], 'safe']"];
        }
        $types = [];
        foreach ($table->columns as $column) {
            if ($this->checkNoRuleAttribute($table, $column->name)) {
                continue;
            }
            switch ($column->type) {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                    $types['integer'][] = $column->name;
                    break;
                case Schema::TYPE_BOOLEAN:
                    $types['boolean'][] = $column->name;
                    break;
                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DOUBLE:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                    $types['number'][] = $column->name;
                    break;
                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                default:
                    $types['safe'][] = $column->name;
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
     * Generates search conditions
     * @return array
     */
    public function generateSearchConditions()
    {
        $columns = [];
        if (($table = $this->getTableSchema()) === false) {
            $class = $this->baseModelClass;
            /* @var $model \yii\base\Model */
            $model = new $class();
            foreach ($model->attributes() as $attribute) {
                $columns[$attribute] = 'unknown';
            }
        } else {
            foreach ($table->columns as $column) {
                if ($this->checkNoSearchAttribute($column->name)) {
                    continue;
                }
                $columns[$column->name] = $column->type;
            }
        }

        $likeConditions = [];
        $hashConditions = [];
        foreach ($columns as $column => $type) {
            switch ($type) {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                case Schema::TYPE_BOOLEAN:
                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DOUBLE:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                    $hashConditions[] = "'{$column}' => \$this->{$column},";
                    break;
                default:
                    $likeConditions[] = "->andFilterWhere(['like', '{$column}', \$this->{$column}])";
                    break;
            }
        }

        $conditions = [];
        if (!empty($hashConditions)) {
            $conditions[] = "\$query->andFilterWhere([\n"
                . str_repeat(' ', 12) . implode("\n" . str_repeat(' ', 12), $hashConditions)
                . "\n" . str_repeat(' ', 8) . "]);\n";
        }
        if (!empty($likeConditions)) {
            $conditions[] = "\$query" . implode("\n" . str_repeat(' ', 12), $likeConditions) . ";\n";
        }

        return $conditions;
    }

    /**
     * @param TableSchema $tableSchema
     * @return array
     */
    protected function getForeignKeys($tableSchema)
    {
        static $foreignKeys = null;

        if ($foreignKeys === null) {
            $foreignKeys = ArrayHelper::getColumn($tableSchema->foreignKeys, function ($element) {
                unset($element[0]);
                $keys = array_keys($element);
                return $keys[0];
            });
        }

        return $foreignKeys;
    }

    /**
     * @param TableSchema $tableSchema
     * @return array
     */
    protected function getForeignKeysTables($tableSchema)
    {
        static $foreignKeys = null;


        if ($foreignKeys === null) {
            $foreignKeys = ArrayHelper::map($tableSchema->foreignKeys, function ($element) {
                unset($element[0]);
                $keys = array_keys($element);
                return $keys[0];
            }, function ($element) {
                return $element[0];
            });
        }

        return $foreignKeys;
    }
}
