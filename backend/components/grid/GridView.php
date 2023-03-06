<?php

namespace backend\components\grid;

use kartik\grid\GridView as BaseGridView;
use Yii;

/**
 * Class GridView
 *
 * @package backend\components\grid
 */
class GridView extends BaseGridView
{
    /**
     * @var array
     */
    public $options = [
        'class' => 'grid-view table-responsive',
    ];

    /**
     * @var bool
     */
    public $bordered = false;

    /**
     * @var bool
     */
    public $resizableColumns = true;

    /**
     * @var string
     */
    public $panelTemplate = "<div class='block block-themed'>{panelHeading}{items}{panelFooter}</div>";

    /**
     * @var string
     */
    public $panelBeforeTemplate = '';

    /**
     * @var string
     */
    public $afterHeader = '';

    /**
     * @var array
     */
    public $tableOptions = [
        'class' => 'table table-striped table-hover table-filtered'
    ];

    /**
     * @var string
     */
    public $dataColumnClass = ModifiedDataColumn::class;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->resizeStorageKey = Yii::$app->user->id . '-' . date("m");
        return parent::init();
    }
}
