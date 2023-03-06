<?php
/**
 * @var $this \yii\web\View
 * @var $model AcceptedCompetitionRequest
 */

use backend\modules\request\models\AcceptedCompetitionRequest;
use yii\helpers\Html;

$controller = $this->context;
$this->title = $model->name . ' - ' . $model->material_label;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', $model->getTitle()), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box box-primary border-black">
    <div class="box-header">
        <h1 class="box-title pull-left"><?= Html::encode($this->title) ?></h1>
        <div class="clearfix"></div>
    </div>

    <div class="box-body">
        <table class="table table-bordered">
            <tr>
                <th><?= bt('Jury') ?></th>
                <th><?= bt('Rating') ?></th>
            </tr>
            <?php foreach ($model->globalRating as $rating): ?>
                <tr>
                    <td><?= $rating->user->username . ' - ' . $rating->user->email ?></td>
                    <td><?= $rating->rating ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

</div>
