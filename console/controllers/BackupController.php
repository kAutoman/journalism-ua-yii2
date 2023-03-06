<?php

namespace console\controllers;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\Connection;
use yii\console\ExitCode;
use yii\console\Controller;
use common\helpers\FileHelper;
use yii\helpers\Console;

class BackupController extends Controller
{
    /**
     * Creates full database and uploads directory backup
     */
    public function actionIndex()
    {
        $rootPath = Yii::getAlias('@root/backups/full')
            . DIRECTORY_SEPARATOR
            . Yii::$app->getFormatter()->asDatetime('now', 'php:Y_m_d_H_i');
        if (!file_exists($rootPath)) {
            mkdir($rootPath, 0775, true);
        }
        if (!is_writable($rootPath)) {
            $this->stdout("Directory is not writable" . PHP_EOL, Console::FG_RED);
            ExitCode::NOPERM;
        }
        $this->actionDump($rootPath);
        $this->actionUploads($rootPath);

        $this->stdout('Full dump created' . PHP_EOL, Console::FG_GREEN);
        ExitCode::OK;
    }

    /**
     * Creates full Db dump and store it in filesystem
     *
     * @param string $destination Dir name where to store dump
     * @param bool $zip Make ZIP file with dump
     * @param string $connection Yii connection component name
     * @throws InvalidConfigException
     */
    public function actionDump(string $destination = '@root/backups/dumps', $zip = true, string $connection = 'db')
    {
        /** @var Connection $db */
        $db = Yii::$app->get($connection);
        $path = Yii::getAlias($destination);
        if (!file_exists($path)) {
            mkdir($path, 0775, true);
        }
        if (!is_writable($path)) {
            $this->stdout("Directory is not writable" . PHP_EOL);
            ExitCode::NOPERM;
        }
        //escape ambiguous characters
        $db->password = strtr($db->password, [
            '(' => '\(',
            ')' => '\(',
            "'" => "\'"
        ]);
        $name = 'dump_' . Yii::$app->getFormatter()->asDatetime('now', 'php:Y_m_d_H_i_s') . '.sql';
        $dumpFile = $path . DIRECTORY_SEPARATOR . $name;
        exec(
            'mysqldump --host='
            . $this->getDsnAttribute('host', $db->dsn)
            . ' --user=' . $db->username
            . ' --password='
            . $db->password
            . ' '
            . $this->getDsnAttribute('dbname', $db->dsn)
            . ' --skip-add-locks > '
            . $dumpFile
        );
        if ($zip) {
            FileHelper::zip($dumpFile, $dumpFile . '.zip');
            unlink($dumpFile);
        }
        $this->stdout("Dump {$name} created at {$path}" . PHP_EOL, Console::FG_BLUE);
        ExitCode::OK;
    }

    /**
     * Creates zip archive with uploads directory backup
     *
     * @param string $destination
     * @throws InvalidConfigException
     */
    public function actionUploads(string $destination = '@root/backups/static')
    {
        $path = Yii::getAlias($destination);
        $uploads = Yii::getAlias('@backend/web/uploads');
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        if (!is_writable($path)) {
            $this->stdout("Directory is not writable" . PHP_EOL);
            ExitCode::NOPERM;
        }
        $name = 'uploads_' . Yii::$app->getFormatter()->asDatetime('now', 'php:Y_m_d_H_i_s') . '.zip';
        if (!FileHelper::zip($uploads, $path . DIRECTORY_SEPARATOR . $name)) {
            $this->stdout('Uploads backup fail' . PHP_EOL, Console::FG_RED);
            ExitCode::UNSPECIFIED_ERROR;
        }
        $this->stdout('Uploads backup done' . PHP_EOL, Console::FG_BLUE);
        ExitCode::OK;
    }

    /**
     * Get connection params from connection dsn property
     * E.g. host, username, password
     *
     * @param string $name Searchable parameter
     * @param string $dsn
     * @return null|string
     */
    private function getDsnAttribute(string $name, string $dsn): ?string
    {
        if (preg_match('/' . $name . '=([^;]*)/', $dsn, $match)) {
            return $match[1];
        } else {
            return null;
        }
    }
}
