<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\components\gii\controllers;

use backend\components\gii\migration\Generator;
use Yii;


/**
 * Class DefaultController
 *
 * @package backend\components\gii\controllers
 */
class DefaultController extends \yii\gii\controllers\DefaultController
{

    /**
     * @param $id
     *
     * @return string
     */
    public function actionView($id)
    {
        $generator = $this->loadGenerator($id);
        $params = ['generator' => $generator, 'id' => $id];

        $preview = Yii::$app->request->post('preview');
        $generate = Yii::$app->request->post('generate');
        $answers = Yii::$app->request->post('answers');

        if ($preview !== null || $generate !== null) {
            if ($generator->validate()) {
                $generator->saveStickyAttributes();
                if ($generator instanceof Generator && !$generator->getTableSchema() && $generator->isSecondStep) {
                    $params['hasError'] = true;
                    $params['results'] = 'You can\'t use second step before creation of table!';

                    return $this->render('@app/components/gii/views/default/view.php', $params);
                }
                $files = $generator->generate();
                if ($generate !== null && !empty($answers)) {
                    if ($generator instanceof \backend\components\gii\removeModules\Generator) {
                        $generator->removeModules();
                    }
                    $params['hasError'] = !$generator->save($files, (array) $answers, $results);
                    $params['results'] = $results;
                    $this->createAndInsertMigration($generator, $params);
                } else {
                    $params['files'] = $files;
                    $params['answers'] = $answers;
                }
            }
        }

        return $this->render('@app/components/gii/views/default/view.php', $params);
    }

    /**
     * @param $generator Generator
     * @param $params array
     */
    protected function createAndInsertMigration($generator, &$params)
    {
        if ($generator instanceof Generator && !$params['hasError'] && $generator->migrationName && !$generator->isSecondStep) {
            $migration = $generator->createMigration();
            if ($migration->up() !== false) {
                $this->insertIntoMigrationTable($generator);
                if ($generator->hasLangTable()) {
                    $migrationLang = $generator->createMigration(true);
                    if ($migrationLang->up() !== false) {
                        $this->insertIntoMigrationTable($generator, true);
                    }
                }
                $generator->isSecondStep = true;
                $params['generator'] = $generator;
            } else {
                $params['hasError'] = true;
                $params['results'] = 'Data base error!';
                unlink($generator->getMigrationAlias());
                if ($generator->hasLangTable()) {
                    unlink($generator->getMigrationAlias(true));
                }
            }
        }
    }

    /**
     * @param Generator $generator
     * @param bool $isLang
     *
     * @throws \yii\db\Exception
     */
    protected function insertIntoMigrationTable($generator, $isLang = false)
    {
        Yii::$app->db->createCommand()->insert(
            '{{%migration}}',
            [
                'version' => $generator->migrationNamespace . $generator->getMigrationName($isLang),
                'apply_time' => time(),
            ]
        )->execute();
    }
}
