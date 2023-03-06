<?php

use backend\components\BackendController;
use backend\widgets\FormLangSwitcher;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\components\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\components\BackendModel|\common\components\model\ActiveRecord */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $controller BackendController */

$this->title = $searchModel->getTitle();
$this->params['breadcrumbs'][] = $this->title;

$controller = $this->context;
?>
<div class="box box-primary border-black">
    <div class="box-header">
        <h1 class="box-title"><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="box-body">
        <div class="row">
            <div class="col-md-3">
                <p>
                    <?php if ($controller->canCreate) : ?>
                        <?= Html::a(Yii::t('back/app', 'Add new'), ['create'], [
                            'class' => 'create btn btn-flat btn-primary black '
                        ]) ?>
                    <?php endif; ?>

                    <?php if ($controller->canExport) : ?>
                        <?= Html::a(Yii::t('back/app', 'Export'), ['export'], [
                            'class' => 'create btn btn-flat btn-success black '
                        ]) ?>
                    <?php endif; ?>
                </p>

            </div>
            <div class="col-md-9"> <?= $searchModel->isTranslatable() ? FormLangSwitcher::widget() : ''; ?></div>
        </div>

        <?= GridView::widget([
            'options' => [
                'class' => 'grid-view table-responsive',
            ],
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => $searchModel->getColumns('index'),
        ]); ?>
    </div>

</div>



