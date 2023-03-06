<?php

namespace console\components;

use Yii;
use yii\helpers\Console;
use yii\base\NotSupportedException;

/**
 * Class Migration
 */
class Migration extends \yii\db\Migration
{
    public $tableOptions;

    public function init()
    {
        parent::init();
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }
        $this->tableOptions = $tableOptions;

        Yii::$app->cacheDb->flush();
    }

    public function column($type, $length = null)
    {
        try {
            return $this->getDb()->getSchema()->createColumnSchemaBuilder($type, $length);
        } catch (NotSupportedException $e) {
            Console::stderr($e->getMessage());
        }
    }

    public function tableExist($table)
    {
        $schema = $this->getDb()->schema;

        return in_array($schema->getRawTableName($table), $schema->tableNames);
    }
}
