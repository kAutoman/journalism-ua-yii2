<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\components\gii\model;

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
    public $ns = 'common\models';
    public $baseClass = 'common\components\model\ActiveRecord';
    public $isSeo = false;
    public $generateLabelsFromComments = true;
    public $useTablePrefix = true;
    public $enableI18N = true;

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Common Model Generator';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator generates an common app ActiveRecord class for the specified database table.';
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates()
    {
        return ['model.php', 'base.php', 'full.php'];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
           // [['createBaseModel', 'hideExistingBaseModel', 'isSeo'], 'boolean'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            //'createBaseModel' => 'Create base model',
            //'hideExistingBaseModel' => 'Hide existing base models',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function hints()
    {
        return array_merge(parent::hints(), [
            //'createBaseModel' => 'Create base model in related <code>base\</code> path with your code, with you can edit',
            //'hideExistingBaseModel' => 'Do not propose to generate existing base models files',
        ]);
    }

//    /**
//     * @inheritdoc
//     */
//    public function stickyAttributes()
//    {
//        return array_merge(parent::stickyAttributes(), ['createBaseModel']);
//    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $files = [];
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
            ];

//            if ($this->createBaseModel) {
//                $baseModelPath = Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/base/' . $className . '.php';
//                if (!$this->hideExistingBaseModel || !file_exists($baseModelPath)) {
//                    $files[] = new CodeFile(
//                        $baseModelPath,
//                        $this->render('base.php', $params)
//                    );
//                }
//
//                $files[] = new CodeFile(
//                    Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/' . $className . '.php',
//                    $this->render('model.php', $params)
//                );
//            } else {
                $files[] = new CodeFile(
                    Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/' . $className . '.php',
                    $this->render('full.php', $params)
                );
//            }
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
     * @inheritdoc
     */
    public function generateRules($table)
    {
        $types = [];
        $lengths = [];
        $other = [];
        $foreignKeys = $this->getForeignKeys($table);
        foreach ($table->columns as $column) {
            if ($column->autoIncrement) {
                continue;
            }
            if ($this->checkNoRuleAttribute($table, $column->name)) {
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

        if ($this->isTranslationTable($tableSchema)) {
            $string .= " . ' [' . \$this->language . ']'";
        }

        return $string;
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
