<?php
/**
 * Created by anatolii
 */
namespace backend\components\gii\migration;


use yii\base\BaseObject;

/**
 * Class ForeignKey
 *
 * @package backend\components\gii\migration
 */
class ForeignKey extends BaseObject
{
    const UPDATE_DELETE_ACTION_CASCADE = 1;
    const UPDATE_DELETE_ACTION_SET_NULL = 2;
    const UPDATE_DELETE_ACTION_RESTRICT = 3;
    const UPDATE_DELETE_ACTION_NO_ACTION = 4;

    /**
     * @var string
     */
    public $fieldName;

    /**
     * @var string
     */
    public $relTableName;

    /**
     * @var string
     */
    public $relTableFieldName;

    /**
     * @var int
     */
    public $delete;

    /**
     * @var int
     */
    public $update;

    /**
     * @var array
     */
    public static $updateDeleteActionTypes = [
        self::UPDATE_DELETE_ACTION_CASCADE => 'CASCADE',
        self::UPDATE_DELETE_ACTION_SET_NULL => 'SET NULL',
        self::UPDATE_DELETE_ACTION_RESTRICT => 'RESTRICT',
        self::UPDATE_DELETE_ACTION_NO_ACTION => 'NO ACTION',
    ];

    /**
     * @return string
     */
    public function getUpdateActionLabel()
    {
        return isset(static::$updateDeleteActionTypes[$this->update])
            ? static::$updateDeleteActionTypes[$this->update]
            : '';
    }

    /**
     * @return string
     */
    public function getDeleteActionLabel()
    {
        return isset(static::$updateDeleteActionTypes[$this->delete])
            ? static::$updateDeleteActionTypes[$this->delete]
            : '';
    }

    /**
     * @param string $tableName
     *
     * @return string
     */
    public function getName($tableName)
    {
        return "fk-{$tableName}-{$this->fieldName}-{$this->relTableName}-{$this->relTableFieldName}";
    }
}
