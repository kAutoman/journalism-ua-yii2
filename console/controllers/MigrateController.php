<?php
/**
 * Author: metal
 * Email: metal
 */

namespace console\controllers;

use Yii;
use yii\console\controllers\MigrateController as YiiMigrateController;
use yii\console\Exception;
use yii\helpers\Console;

/**
 * Manages application migrations. Extends yii migrate controller
 *
 * Class MigrateController
 * @package console\controllers
 */
class MigrateController extends YiiMigrateController
{
    /**
     * Generate language migration or not
     *
     * @var bool
     */
    public $lang = false;

    /**
     * Path alias to the language migration template
     *
     * @var string
     */
    public $templateFileLang = '@console/components/migration/template_translation.php';

    /**
     * @inheritdoc
     */
    public $templateFile = '@console/components/migration/template.php';

    /**
     * Path alias to the migration template for column adding
     *
     * @var string
     */
    public $templateAddColumnFile = '@console/components/migration/template_add_column.php';

    /**
     * Path alias to the migration template for column adding
     *
     * @var string
     */
    public $templateAddColumnFileLang = '@console/components/migration/template_add_column_translation.php';

    /**
     * Path alias to the migration template for inserting
     *
     * @var string
     */
    public $templateInsert = '@console/components/migration/template_insert.php';

    /**
     * @var string
     */
    public $baseAppMigrationPath = '@console/migrations';


    /**
     * @inheritdoc
     */
    public function options($actionId)
    {
        return array_merge(
            parent::options($actionId),
            ($actionId == 'create') ? ['lang'] : [] // action create
        );
    }

    /**
     * @inheritdoc
     */
    public function actionCreate($name, $tableName = false)
    {
        if (!preg_match('/^\w+$/', $name)) {
            throw new Exception('The migration name should contain letters, digits and/or underscore characters only.');
        }

        if ($tableName === false) {
            $tableName = $this->prompt('Enter a table name used in migration (without prefix):', ['default' => '*tableName*']);
            $this->stdout("\n");
        }

        $originName = $name;
        $name = 'm' . gmdate('ymd_His') . '_' . $originName;

        $this->migrationPath = Yii::getAlias($this->baseAppMigrationPath);

        $file = $this->migrationPath . DIRECTORY_SEPARATOR . $name . '.php';

        if ($this->confirm("Create new migration '{$file}' with used table name '{$tableName}'?", true)) {
            $langTemplate = null;
            switch (true) {
                case (bool) substr_count($name, 'add_column'):
                    $template = $this->templateAddColumnFile;
                    $langTemplate = $this->templateAddColumnFileLang;
                    break;
                case (bool) substr_count($name, 'insert'):
                    $template = $this->templateInsert;
                    break;
                default:
                    $template = $this->templateFile;
                    $langTemplate = $this->templateFileLang;
            }
            $content = $this->renderFile(
                Yii::getAlias($template),
                ['className' => $name, 'tableName' => $tableName]
            );
            file_put_contents($file, $content);
            $this->stdout("New migration created successfully.\n", Console::FG_GREEN);

            if ($this->lang && $langTemplate) {
                $name = 'm' . gmdate('ymd_His', strtotime('+10 second')) . '_' . $originName . '_translation';
                $file = $this->migrationPath . DIRECTORY_SEPARATOR . $name . '.php';
                $content = $this->renderFile(
                    Yii::getAlias($langTemplate),
                    ['className' => $name, 'tableName' => $tableName]
                );
                file_put_contents($file, $content);
                $this->stdout("New language migration created successfully.\n", Console::FG_GREEN);
            }
        }
    }
}
