<?php

namespace backend\actions;

use kartik\grid\controllers\ExportController;
use yii\base\UnknownClassException;
use yii\web\NotFoundHttpException;
use yii2tech\spreadsheet\Spreadsheet;

/**
 * Class ActionExport
 *
 * @package backend\actions
 */
class ActionExport extends ActionCRUD
{
    public $exportMethod = 'getExportConfig';

    public $exportQueryMethod = 'getExportQuery';

    public $exportFileName = 'export.xls';

    /**
     * @inheritdoc
     * @throws UnknownClassException
     */
    public function init()
    {
        if ($this->modelClass === null) {
            throw new UnknownClassException(__CLASS__ . '::$modelClass must be set.');
        }

        $this->model = new $this->modelClass;

        if ($this->model === null) {
            throw new UnknownClassException($this->modelClass . 'must be exists.');
        }

        if (!method_exists($this->model, $this->exportMethod)) {
            throw new UnknownClassException($this->modelClass . ' don`t has method ' . $this->exportMethod);
        }
    }

    /**
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function run()
    {
        if ($this->model == null) {
            throw new NotFoundHttpException;
        }

        $columns = call_user_func([$this->model, $this->exportMethod]);

        $query = $this->model::find();

        $query = call_user_func([$this->model, $this->exportQueryMethod], $query);

        $exporter = new Spreadsheet([
            'query' => $query,
            'columns' => $columns
        ]);

        $exporter->send($this->exportFileName);
    }
}
