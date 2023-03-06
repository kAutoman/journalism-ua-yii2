<?php
/**
 * Created by anatolii
 */
namespace backend\components\gii\migration;


use yii\base\BaseObject;

/**
 * Class Field
 *
 * @package backend\components\gii\migration
 */
class Field extends BaseObject
{
    const TYPE_PRIMARY_KEY = 1;
    const TYPE_STRING = 2;
    const TYPE_TEXT = 3;
    const TYPE_INTEGER = 4;
    const TYPE_SMALL_INTEGER = 5;
    const TYPE_BOOLEAN = 6;
    const TYPE_FLOAT = 7;
    const TYPE_DECIMAL = 8;
    const TYPE_DATE = 9;
    const TYPE_DATETIME = 10;

    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $type;

    /**
     * @var string
     */
    public $params;

    /**
     * @var bool
     */
    public $isNotNull = false;

    /**
     * @var mixed
     */
    public $defaultValue;

    /**
     * @var bool
     */
    public $isUnsigned = false;

    /**
     * @var bool
     */
    public $isUnique = false;

    /**
     * @var string
     */
    public $comment;

    /**
     * @var bool
     */
    public $isIndex = false;

    /**
     * @var bool
     */
    public $isLang = false;

    /**
     * @var array
     */
    public static $types = [
        self::TYPE_PRIMARY_KEY => 'Primary key',
        self::TYPE_STRING => 'String',
        self::TYPE_TEXT => 'Text',
        self::TYPE_INTEGER => 'Integer',
        self::TYPE_SMALL_INTEGER => 'Small integer',
        self::TYPE_BOOLEAN => 'Boolean',
        self::TYPE_FLOAT => 'Float',
        self::TYPE_DECIMAL => 'Decimal',
        self::TYPE_DATE => 'Date',
        self::TYPE_DATETIME => 'Datetime',
    ];

    /**
     * @return string
     */
    public function getTypeOutput()
    {
        switch ($this->type) {
            case self::TYPE_PRIMARY_KEY:
                $type = 'primaryKey';
                break;
            case self::TYPE_TEXT:
                $type = 'text';
                break;
            case self::TYPE_INTEGER:
                $type = 'integer';
                break;
            case self::TYPE_SMALL_INTEGER:
                $type = 'smallInteger';
                break;
            case self::TYPE_BOOLEAN:
                $type = 'boolean';
                break;
            case self::TYPE_FLOAT:
                $type = 'float';
                break;
            case self::TYPE_DECIMAL:
                $type = 'decimal';
                break;
            case self::TYPE_DATE:
                $type = 'date';
                break;
            case self::TYPE_DATETIME:
                $type = 'dateTime';
                break;
            default:
                $type = 'string';
        }

        return "$type({$this->params})";
    }

    /**
     * @return string
     */
    public function getNullOutput()
    {
        if ($this->isPrimaryKey()) {
            return '';
        }

        return $this->isNotNull ? '->notNull()' : '->null()';
    }

    /**
     * @return string
     */
    public function getDefaultValueOutput()
    {
        if (!$this->isPrimaryKey() && ($this->defaultValue || $this->defaultValue == '0')) {
            $value = in_array($this->type, [self::TYPE_STRING, self::TYPE_TEXT, self::TYPE_DATE, self::TYPE_DATETIME])
                ? "('{$this->defaultValue}')"
                : "({$this->defaultValue})";

            return '->defaultValue' . $value;
        }

        return '';
    }

    /**
     * @return string
     */
    public function getUnsignedOutput()
    {
        return $this->isUnsigned && !$this->isPrimaryKey() ? '->unsigned()' : '';
    }

    /**
     * @return string
     */
    public function getUniqueOutput()
    {
        return $this->isUnique && !$this->isPrimaryKey() && !$this->isIndex ? '->unique()' : '';
    }

    /**
     * @return string
     */
    public function getCommentOutput()
    {
        return $this->comment ? "->comment('{$this->comment}')" : '';
    }

    /**
     * @return array
     */
    public static function getDefaultFieldConfigs()
    {
        return [
            [
                'name' => 'id',
                'type' => self::TYPE_PRIMARY_KEY
            ],
            [
                'name' => 'label',
                'type' => self::TYPE_STRING,
                'isNotNull' => true,
                'comment' => 'Label'
            ],
            [
                'name' => 'content',
                'type' => self::TYPE_TEXT,
                'comment' => 'Content'
            ],
            [
                'name' => 'published',
                'type' => self::TYPE_BOOLEAN,
                'isNotNull' => true,
                'defaultValue' => 1,
                'comment' => 'Published'
            ],
            [
                'name' => 'position',
                'type' => self::TYPE_INTEGER,
                'isNotNull' => true,
                'defaultValue' => 0,
                'comment' => 'Position'
            ],
            [
                'name' => 'created_at',
                'type' => self::TYPE_INTEGER,
                'isNotNull' => true,
                'comment' => 'Created At'
            ],
            [
                'name' => 'updated_at',
                'type' => self::TYPE_INTEGER,
                'isNotNull' => true,
                'comment' => 'Updated At'
            ],
        ];
    }

    /**
     * @return bool
     */
    public function isPrimaryKey()
    {
        return $this->type == self::TYPE_PRIMARY_KEY;
    }
}
