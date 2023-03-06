<?php
/**
 * Created by anatolii
 */

namespace backend\components\gii\migration;

use Yii;
use yii\base\NotSupportedException;
use yii\db\ColumnSchema;
use yii\db\Schema;
use yii\db\TableSchema;
use yii\gii\CodeFile;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\VarDumper;
use yii\validators\RequiredValidator;

/**
 * Class Generator generates model for static pages
 *
 * @package backend\components\gii\migration
 */
class Generator extends \yii\gii\generators\model\Generator
{
    /**
     * @var string
     */
    public $ns;

    /**
     * @var string
     */
    public $modelClass;

    /**
     * @var string
     */
    public $controllerClass;

    /**
     * @var string
     */
    public $baseControllerClass = '\backend\components\BackendController';

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $tableName;

    /**
     * @var string
     */
    public $migrationName;

    /**
     * @var array
     */
    public $fields = [];

    /**
     * @var array
     */
    public $foreignKeys = [];

    /**
     * @var bool
     */
    public $isSeo = false;

    /**
     * @var bool
     */
    public $enableAjaxValidation = false;

    /**
     * @var array
     */
    public $imageUploaders = [];

    /**
     * @var array
     * @deprecated
     */
    public $relationsForRelatedFormWidget = [];

    /**
     * @var bool
     */
    public $isSecondStep = false;

    /**
     * @var string
     */
    public $moduleId;

    /**
     * @var bool
     */
    public $generateLabelsFromComments = true;

    /**
     * @var bool
     */
    public $useTablePrefix = true;

    /**
     * @var string
     */
    public $migrationNamespace = '\\console\\migrations\\';

    /**
     * @var string
     */
    public $langTableSuffix = 'Lang';

    /**
     * @var bool|TableSchema
     */
    protected $tableSchema = false;

    /**
     * @var bool|TableSchema
     */
    protected $langTableSchema = false;

    /**
     * @var bool|null
     */
    protected $hasLangTable = null;

    /**
     * @var array
     */
    protected $sluggableNames = ['alias', 'slug'];


    public function init()
    {
        parent::init();
        $this->fields = Field::getDefaultFieldConfigs();
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Migration Generator';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator generates migration';
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates()
    {
        return ['migration_templates/template.php', 'model.php', 'controller.php', 'common_model.php'];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ns', 'modelClass', 'controllerClass'], 'required'],
            [['tableName', 'migrationName'], 'required'],
            [['fields', 'foreignKeys', 'imageUploaders'], 'validateFields'],
            [['ns', 'modelClass', 'title'], 'filter', 'filter' => 'trim'],
            [['ns'], 'filter', 'filter' => function ($value) {
                    return trim($value, '\\');
                }
            ],
            [['ns', 'controllerClass'], 'match', 'pattern' => '/^[\w\\\\]+$/', 'message' => 'Only word characters and backslashes are allowed.'],
            [['ns'], 'validateNamespace'],
            [['modelClass'], 'match', 'pattern' => '/^\w+$/', 'message' => 'Only word characters are allowed.'],
            [['title', 'tableName', 'migrationName', 'moduleId'], 'string'],
            [['isSeo', 'isSecondStep', 'enableI18N', 'generateLabelsFromComments', 'enableAjaxValidation'], 'boolean'],
            [['messageCategory'], 'validateMessageCategory', 'skipOnEmpty' => false],
            [['foreignKeys', 'imageUploaders'], 'safe'],
        ];
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
                if (in_array($key, ['fieldName', 'type'])) {
                    if (!$requiredValidator->validate($value, $error)) {
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
            'imageUploaders' => 'Add Ajax multi-(or single) upload widget',
            'isSecondStep' => 'Is second step of generating (models generating)',
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
        if ($this->foreignKeys === '') {
            $this->foreignKeys = [];
        }
        if ($this->imageUploaders === '') {
            $this->imageUploaders = [];
        }
        $files = [];
        if (!$this->isSecondStep) {
            //For updating table if it exists
            if (Yii::$app->db->getTableSchema($this->tableName)) {
                $migrationsNames = scandir(Yii::getAlias('@console/migrations/'));
                $migrationsForRevert = preg_grep("/create_{$this->tableName}_table/", $migrationsNames);
                $migrationsForRevert = array_reverse($migrationsForRevert);
                foreach ($migrationsForRevert as $item) {
                    $this->migrateDown($item);
                }
            }
            //Migration generation
            $files[] = new CodeFile(
                $this->getMigrationAlias(),
                $this->render('migration_templates/template.php')
            );
            if ($this->hasLangTable()) {
                $files[] = new CodeFile(
                    $this->getMigrationAlias(true),
                    $this->render('migration_templates/template_translation.php')
                );
            }
        } else {
            //Controller generation
            $files[] = new CodeFile(
                Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->controllerClass, '\\')) . '.php'),
                $this->render('controller.php')
            );

            //Common model generation
            $relations = $this->generateRelations();
            $db = Yii::$app->db;
            $className = $this->generateClassName($this->tableName);
            $tableSchema = $this->getTableSchema();
            $translationTableSchema = $db->getTableSchema($this->tableName . '_lang');
            $params = [
                'tableName' => $this->tableName,
                'className' => $className,
                'tableSchema' => $tableSchema,
                'labels' => $this->generateLabels($tableSchema),
                'rules' => $this->generateRules($tableSchema),
                'relations' => isset($relations[$this->tableName]) ? $this->checkMultiLangRelation($className, $relations[$this->tableName]) : [],
                'multiLanguageModel' => $this->isMultiLanguageTable($tableSchema),
                'behaviors' => $this->generateBehaviors($tableSchema, true),
                'translationAttributes' => $this->isMultiLanguageTable($tableSchema) ? $this->getTranslationAttributes($tableSchema, $translationTableSchema) : [],
            ];
            $files[] = new CodeFile(
                Yii::getAlias('@common/models') . '/' . $this->modelClass . '.php',
                $this->render('common_model.php', $params)
            );

            //Crud generation
            $params = [
                'tableName' => $this->tableName,
                'className' => $className,
                'tableSchema' => $tableSchema,
                'labels' => $this->generateLabels($tableSchema),
                'rules' => $this->generateRules($tableSchema),
                'relations' => isset($relations[$this->tableName]) ? $this->checkMultiLangRelation($className, $relations[$this->tableName]) : [],
                'multiLanguageModel' => $this->isMultiLanguageTable($tableSchema),
                'behaviors' => $this->generateBehaviors($tableSchema),
                'translationAttributes' => $this->isMultiLanguageTable($tableSchema) ? $this->getTranslationAttributes($tableSchema, $translationTableSchema) : [],
                'viewColumns' => $this->getViewColumns(),
                'indexColumns' => $this->getIndexColumns(),
                'formColumns' => $this->getFormColumns(),
                'hasSluggable' => $this->checkSluggable(),
            ];

            $files[] = new CodeFile(
                Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/' . $className . '.php',
                $this->render('../../crud/default/full.php', $params)
            );

            if ($this->isMultiLanguageTable($tableSchema)) {
                $translateParams = [
                    'tableName' => $this->tableName . '_lang',
                    'className' => $className . 'Lang',
                    'tableSchema' => $translationTableSchema,
                    'labels' => $this->generateLabels($translationTableSchema),
                    'rules' => $this->generateRules($translationTableSchema),
                    'multiLanguageModel' => false,
                ];
                $files[] = new CodeFile(
                    Yii::getAlias('@common/models') . '/lang/' . $this->modelClass . 'Lang.php',
                    $this->render('common_lang_model.php', $translateParams)
                );
//                $files[] = new CodeFile(
//                    Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/' . $className . 'Lang.php',
//                    $this->render('../../crud/default/translation.php', $translateParams)
//                );
            }

            $translationTypes = $this->getTranslationTypes($translationTableSchema);
            $searchRules = $this->generateSearchRules();
            if ($this->hasLangTable) {
                $searchRules = ArrayHelper::merge($this->generateSearchRules(), [
                    "[['" . implode('\', \'', array_keys($translationTypes)) . "'], 'safe']"
                ]);
            }

            $searchModel = Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/' . $className . 'Search.php';
            $files[] = new CodeFile(
                $searchModel,
                $this->render(
                    '../../crud/default/search.php',
                    [
                        'rules' => $searchRules,
                        'searchConditions' => $this->generateSearchConditions(),
                        'hasLangTable' => $this->hasLangTable,
                        'translationTypes' => $translationTypes,
                    ]
                )
            );
        }

        return $files;
    }

    /**
     * Generates the attribute labels for the specified table.
     * @param \yii\db\TableSchema $table the table schema
     * @return array the generated attribute labels (name => label)
     */
    public function generateLabels($table)
    {
        $labels = [];
        $columns = $table->columns;
        if ($this->hasLangTable()) {
            $langColumns = $this->getLangTableSchema()->columns;
            unset($langColumns['model_id'], $langColumns['language']);
            $columns = ArrayHelper::merge($columns, $langColumns);
        }
        foreach ($columns as $column) {
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
     * @param $translationTableSchema
     *
     * @return mixed
     */
    public function getTranslationTypes($translationTableSchema)
    {
        if (!$this->hasLangTable) {
            return false;
        }
        $columns = [];
        foreach ($translationTableSchema->columns as $column) {
            if (!in_array($column->name, ['model_id', 'language'])) {
                $columns[$column->name] = $column->phpType;
            }
        }

        return $columns;
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
     * @param bool $isLang
     *
     * @return string
     */
    public function getMigrationName($isLang = false)
    {
        return $isLang ? $this->migrationName . '_lang' : $this->migrationName;
    }

    /**
     * Creates a new migration instance.
     *
     * @param bool $isLang
     *
     * @return \yii\db\Migration the migration instance
     */
    public function createMigration($isLang = false)
    {
        require_once($this->getMigrationAlias($isLang));
        $className = $this->migrationNamespace . $this->getMigrationName($isLang);

        return new $className(['db' => Yii::$app->db]);
    }

    /**
     * @param bool $isLang
     *
     * @return bool|string
     */
    public function getMigrationAlias($isLang = false)
    {
        return Yii::getAlias('@console/migrations/' . $this->getMigrationName($isLang) . '.php');
    }


    /**
     * @param $migrationName
     *
     * @return bool
     * @throws \yii\db\Exception
     */
    protected function migrateDown($migrationName)
    {

        $class = $this->migrationNamespace . str_replace('.php', '', $migrationName);
        $migrationPath = Yii::getAlias('@console/migrations/' . $migrationName);
        require_once($migrationPath);
        /** @var \yii\db\Migration $migration */
        $migration = new $class(['db' => Yii::$app->db]);
        if ($migration->down() !== false) {
            Yii::$app->db->createCommand()->delete(
                '{{%migration}}',
                [
                    'version' => $class,
                ]
            )->execute();
            unlink($migrationPath);

            return true;
        }

        return false;
    }

    /**
     * @param \yii\db\TableSchema $table the table schema
     * @param bool $forCommonModel
     *
     * @return array generated behaviors
     */
    public function generateBehaviors($table, $forCommonModel = false)
    {
        $behaviors = [];
        $timestamp = [];
        foreach ($table->columns as $column) {
            if (in_array($column->name, ['created_at', 'updated_at'], true)) {
                $timestamp[] = $column->name;
            }
        }
        if (is_array($timestamp) && !empty($timestamp)) {
            $code = "'timestamp' => [
                'class' => TimestampBehavior::class,";
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
            $behaviors['timestamp'] = $code;
        }
        if ($this->hasLangTable()) {
            $code = "'translated' => [
                'class' => TranslatedBehavior::class, 
                'translateAttributes' => \$this->getLangAttributes() 
            ]";
            $behaviors['translated'] = $code;
        }

//        if ($this->isSeo && !$forCommonModel) {
//            $code = "'seo' => [
//                'class' => \\notgosu\\yii2\\modules\\metaTag\\components\\MetaTagBehavior::className(),
//            ]";
//            $behaviors[] = $code;
//        }


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
            if ($relation[1] === $class . 'Lang') {
//                $newRelations['Lang'] = $relation;
            } else {
                $newRelations[$name] = $relation;
            }
        }

        return $newRelations;
    }

    /**
     * @param \yii\db\TableSchema $table the table schema
     * @param \yii\db\TableSchema $langTable the table schema
     *
     * @return array
     */
    public function getTranslationAttributes($table, $langTable)
    {
        if (!$langTable) {
            return [];
        }
        $attributes = [];
        foreach ($langTable->getColumnNames() as $column) {
            if (!in_array($column, ['model_id', 'language'])) {
                $attributes[] = $column;
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
        return (boolean)$db->getTableSchema($table->name . '_lang');
    }

    /**
     * @param TableSchema $table
     * @return bool
     */
    public function isTranslationTable($table)
    {
        $db = $this->getDbConnection();
        $baseTableName = preg_replace('/'. preg_quote('_lang', '/') . '$/', '', $table->name);
        return $this->endsWith($table->name, '_lang') && $db->getTableSchema($baseTableName);
    }

    /**
     * @param $haystack
     * @param $needle
     * @return bool
     */
    public function endsWith($haystack, $needle) {
        $haystackLen = strlen($haystack);
        $needleLen = strlen($needle);
        if ($needleLen > $haystackLen) return false;
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

        return $string;
    }

    /**
     * Returns table schema for current model class or false if it is not an active record
     * @return boolean|\yii\db\TableSchema
     */
    public function getTableSchema()
    {
        if (!$this->tableSchema) {
            $db = $this->getDbConnection();
            $this->tableSchema = $db->getTableSchema($this->tableName, true);
        }

        return $this->tableSchema;
    }

    /**
     * Returns table schema for current model class or false if it is not an active record
     * @return boolean|\yii\db\TableSchema
     */
    public function getLangTableSchema()
    {
        if (!$this->langTableSchema) {
            $db = $this->getDbConnection();
            $this->langTableSchema = $db->getTableSchema($this->tableName . '_lang', true);
        }

        return $this->langTableSchema;
    }

    /**
     * @return array|bool model column names
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
     * @param $attribute
     * @return bool
     */
    public function checkNoIndexAttribute($attribute)
    {
        static $attributes = [
            'id',
            'created_at',
            'updated_at',
            'model_id',
            'language',
            'content',
            'description'
        ];

        return in_array($attribute, $attributes, true);
    }

    /**
     * @param $attribute
     * @return bool
     */
    public function checkNoFormAttribute($attribute)
    {
        static $attributes = [
            'id',
            'created_at',
            'updated_at',
            'model_id',
            'language',
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
            'model_id',
            'language',
        ];

        return in_array($attribute, $attributes, true);
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
            'model_id',
            'language',
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
                    'type' => FormBuilder::INPUT_PASSWORD,
                ]";
            } else {
                return "[
                    'type' => FormBuilder::INPUT_TEXT,
                ]";
            }
        }
        $column = $tableSchema->columns[$attribute];
        $foreignKeys = $this->getForeignKeys($tableSchema);
        if ($attribute === 'published') {
            return "[
                    'type' => FormBuilder::INPUT_CHECKBOX,
                ]";
        } elseif ($column->phpType === 'boolean' || ($column->type === Schema::TYPE_SMALLINT && $column->size === 1)) {
            return "[
                    'type' => FormBuilder::INPUT_CHECKBOX,
                ]";
        } elseif (stripos($column->name, 'file') !== false) {
            return "[
                    'type' => FormBuilder::INPUT_FILE,
                ]";
        } elseif ($column->type === Schema::TYPE_DATE) {
            return "[
                    'type' => FormBuilder::INPUT_WIDGET,
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
                    'type' => FormBuilder::INPUT_WIDGET,
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
                    'type' => FormBuilder::INPUT_WIDGET,
                    'widgetClass' => \\metalguardian\\dateTimePicker\\Widget::className(),
                    'options' => [
                        'options' => [
                            'class' => 'form-control',
                        ],
                    ],
                ]";
        } elseif ($column->type === 'text') {
            return "[
                    'type' => FormBuilder::INPUT_TEXTAREA
                ]";
        } elseif (in_array($column->name, $foreignKeys, true)) {
            $foreignKeysTables = $this->getForeignKeysTables($tableSchema);
            $relTableName = isset($foreignKeysTables[$column->name]) ? $foreignKeysTables[$column->name] : null;
            if ($relTableName) {
                $relClassName = '\\common\\models\\' . $this->generateClassName($relTableName);
                return "[
                    'type' => FormBuilder::INPUT_DROPDOWN_LIST,
                    'items' => {$relClassName}::getListItems(),
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
                    'type' => FormBuilder::INPUT_DROPDOWN_LIST,
                    'items' => " . preg_replace("/\n\s*/", ' ', VarDumper::export($dropDownOptions)) . ",
                    'options' => [
                        'prompt' => '',
                    ],
                ]";
            } elseif ($column->phpType !== 'string' || $column->size === null) {
                return "[
                    'type' => FormBuilder::{$input},
                ]";
            } else {
                return "[
                    'type' => FormBuilder::{$input},
                ]";
            }
        }
    }

    /**
     * @return array the generated relation declarations
     */
    protected function generateRelations()
    {
        if ($this->generateRelations === self::RELATIONS_NONE) {
            return [];
        }

        $db = $this->getDbConnection();
        $relations = [];
        $schemaNames = $this->getSchemaNames();
        foreach ($schemaNames as $schemaName) {
            foreach ($db->getSchema()->getTableSchemas($schemaName) as $table) {
                $className = $this->generateClassName($table->fullName);
                foreach ($table->foreignKeys as $refs) {
                    $refTable = $refs[0];
                    $refTableSchema = $db->getTableSchema($refTable);
                    if ($refTableSchema === null) {
                        // Foreign key could point to non-existing table: https://github.com/yiisoft/yii2-gii/issues/34
                        continue;
                    }
                    unset($refs[0]);
                    $fks = array_keys($refs);
                    $refClassName = $this->generateClassName($refTable);

                    // Add relation for this table
                    $link = $this->generateRelationLink(array_flip($refs));
                    $relationName = $this->generateRelationName($relations, $table, $fks[0], false);
                    $relations[$table->fullName][$relationName] = [
                        "return \$this->hasOne($refClassName::class, $link);",
                        $refClassName,
                        false,
                    ];

                    // Add relation for the referenced table
                    $hasMany = $this->isHasManyRelation($table, $fks);
                    $link = $this->generateRelationLink($refs);
                    $relationName = $this->generateRelationName($relations, $refTableSchema, $className, $hasMany);
                    $relations[$refTableSchema->fullName][$relationName] = [
                        "return \$this->" . ($hasMany ? 'hasMany' : 'hasOne') . "($className::class, $link);",
                        $className,
                        $hasMany,
                    ];
                }

                if (($junctionFks = $this->checkJunctionTable($table)) === false) {
                    continue;
                }

                $relations = $this->generateManyManyRelations($table, $junctionFks, $relations);
            }
        }

        if ($this->generateRelations === self::RELATIONS_ALL_INVERSE) {
            return $this->addInverseRelations($relations);
        }

        return $relations;
    }

    /**
     * Generates relations using a junction table by adding an extra viaTable().
     * @param \yii\db\TableSchema the table being checked
     * @param array $fks obtained from the checkJunctionTable() method
     * @param array $relations
     * @return array modified $relations
     */
    private function generateManyManyRelations($table, $fks, $relations)
    {
        $db = $this->getDbConnection();

        foreach ($fks as $pair) {
            list($firstKey, $secondKey) = $pair;
            $table0 = $firstKey[0];
            $table1 = $secondKey[0];
            unset($firstKey[0], $secondKey[0]);
            $className0 = $this->generateClassName($table0);
            $className1 = $this->generateClassName($table1);
            $table0Schema = $db->getTableSchema($table0);
            $table1Schema = $db->getTableSchema($table1);

            // @see https://github.com/yiisoft/yii2-gii/issues/166
            if ($table0Schema === null || $table1Schema === null) {
                continue;
            }

            $link = $this->generateRelationLink(array_flip($secondKey));
            $viaLink = $this->generateRelationLink($firstKey);
            $relationName = $this->generateRelationName($relations, $table0Schema, key($secondKey), true);
            $relations[$table0Schema->fullName][$relationName] = [
                "return \$this->hasMany($className1::class, $link)->viaTable('"
                . $this->generateTableName($table->name) . "', $viaLink);",
                $className1,
                true,
            ];

            $link = $this->generateRelationLink(array_flip($firstKey));
            $viaLink = $this->generateRelationLink($secondKey);
            $relationName = $this->generateRelationName($relations, $table1Schema, key($firstKey), true);
            $relations[$table1Schema->fullName][$relationName] = [
                "return \$this->hasMany($className0::class, $link)->viaTable('"
                . $this->generateTableName($table->name) . "', $viaLink);",
                $className0,
                true,
            ];
        }

        return $relations;
    }


    /**
     * @return array
     */
    public function getViewColumns()
    {
        $columns = [];
        if ($this->hasLangTable) {
            $columnNames = array_merge($this->getLangTableSchema()->getColumnNames(), $this->getColumnNames());
        } else {
            $columnNames = $this->getColumnNames();
        }

        foreach ($columnNames as $column) {
            if ($this->checkNoViewAttribute($column)) {
                continue;
            }
            if ($this->hasLangTable && in_array($column, $this->getLangTableSchema()->getColumnNames())) {
                $columns[] = in_array($column, ['published']) ? "'{$column}:boolean'" : "'$column'";
            } else {
                $columns[] = in_array($column, ['published']) ? "'{$column}:boolean'" : "'$column'";
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
        if ($this->hasLangTable) {
            $columnNames = array_merge($this->getLangTableSchema()->getColumnNames(), $this->getColumnNames());
        } else {
            $columnNames = $this->getColumnNames();
        }

        foreach ($columnNames as $column) {
            if ($this->checkNoIndexAttribute($column)) {
                continue;
            }
            if ($this->hasLangTable && in_array($column, $this->getLangTableSchema()->getColumnNames())) {
                $columns[] = in_array($column, ['published']) ? "{$column}:boolean" : $column;
            } else {
                $columns[] = in_array($column, ['published']) ? "{$column}:boolean" : $column;
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
        if ($this->hasLangTable) {
            $columnNames = array_merge($this->getLangTableSchema()->getColumnNames(), $this->getColumnNames());
        } else {
            $columnNames = $this->getColumnNames();
        }
        foreach ($columnNames as $attribute) {
            if (!$this->checkNoFormAttribute($attribute)) {
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
                case Schema::TYPE_TINYINT:
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

        if ($this->hasLangTable) {
            foreach ($this->getTranslationTypes($this->getLangTableSchema()) as $attr => $type) {
                $likeConditions[] = "->andFilterWhere(['like', \"{\$langTable}.{$attr}\", \$this->{$attr}])";
            }//"{$langTable}.content"
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

    /**
     * @inheritdoc
     */
    public function generateRules($table)
    {
        $types = [];
        $lengths = [];
        $other = [];
        $foreignKeys = $this->getForeignKeys($table);
        $columns = $table->columns;
        if ($this->hasLangTable()) {
            $langColumns = $this->getLangTableSchema()->columns;
            unset($langColumns['model_id'], $langColumns['language']);
            $columns = ArrayHelper::merge($columns, $langColumns);
        }
        foreach ($columns as $column) {
            if ($column->autoIncrement || $this->checkNoRuleAttribute($table, $column->name)) {
                continue;
            }
            if (!$column->allowNull && $column->defaultValue === null && $column->name !== 'alias') {
                $types['required'][] = $column->name;
            }
            switch ($column->type) {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                    $types['integer'][] = $column->name;
                    break;
                case Schema::TYPE_BOOLEAN:
                case Schema::TYPE_TINYINT:
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
            if (in_array($column->name, ['alias'], true)) {
                $other[] = "[['" . $column->name . "'], 'match', 'pattern' => Pattern::alias()]";
            }
            if (in_array($column->name, $foreignKeys, true)) {
                $foreignKeysTables = $this->getForeignKeysTables($table);
                $relTableName = isset($foreignKeysTables[$column->name]) ? $foreignKeysTables[$column->name] : null;
                if ($relTableName) {
                    $relClassName = '\\common\\models\\' . $this->generateClassName($relTableName);
                    $other[] = "[['" . $column->name . "'], 'exist', 'targetClass' => " . $relClassName . "::class, 'targetAttribute' => 'id']";
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
     * @param string $string
     *
     * @return string
     */
    public static function formatToConstant($string)
    {
        $string = static::camelCase($string);
        $string = static::camelcaseToUnderscore($string);

        return strtoupper($string);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public static function camelcaseToUnderscore($string)
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
    public static function camelCase($str, $noStrip = [])
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
     * @param string $string
     *
     * @return string
     */
    public static function getRelationMethodName($string)
    {
        $string = static::camelCase($string);

        return 'get' . ucfirst($string);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public static function getSaveAttributeConstantName($string)
    {
        $string = static::formatToConstant($string);

        return 'SAVE_ATTRIBUTE_' . $string;
    }

    /**
     * @return bool
     */
    private function checkSluggable(): bool
    {
        return !empty(array_intersect($this->sluggableNames, $this->getColumnNames()));
    }
}
