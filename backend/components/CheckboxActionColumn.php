<?php
/**
 * 
 */

namespace backend\components;

use backend\helpers\CheckboxHelper;
use yii\grid\DataColumn;

/**
 * Class CheckboxActionColumn
 */
class CheckboxActionColumn extends DataColumn
{
    public $contentOptions = [
        'class' => 'text-center',
    ];

    /**
     * @var string
     */
    public $action = 'change';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (empty($this->filter)) {
            $this->filter = \Yii::$app->formatter->booleanFormat;
        }
    }

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        return CheckboxHelper::renderCheckboxCell($model, $this->attribute, $key, $this->action);
    }
}
