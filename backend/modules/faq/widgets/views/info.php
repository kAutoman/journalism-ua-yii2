<?php

use yii\web\View;

/**
 * @var View $this
 * @var string $bgColor
 * @var string $iconClass
 * @var string $title
 * @var string $countQuery
 * @var string $subContent
 * @var string $link
 */
?>

<div class="small-box bg-<?= $bgColor; ?>">
    <div class="inner">
        <h3><?= $countQuery; ?></h3>
        <p><?= $title; ?></p>
    </div>
    <div class="icon">
        <i class="<?= $iconClass; ?>"></i>
    </div>
    <a href="<?= $link; ?>" class="small-box-footer">
        <?= bt('More info'); ?> <i class="fa fa-arrow-circle-right"></i>
    </a>
</div>
