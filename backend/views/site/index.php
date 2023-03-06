<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';

use backend\modules\faq\models\FaqAskQuestion;
use backend\modules\faq\widgets\RequestsInfoBox;

?>

<div class="site-index">
    <div class="row">
        <div class="col-md-3">Hello admin</div>
        <div class="col-md-3"></div>
        <div class="col-md-3"></div>
        <div class="col-md-3">
            <?= RequestsInfoBox::widget(['countQuery' => FaqAskQuestion::getRequestsCount()]); ?>
        </div>
    </div>
   
</div>
