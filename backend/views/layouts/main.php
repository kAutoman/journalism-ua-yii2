<?php

use backend\assets\AppAsset;
use backend\widgets\Alert;
use common\helpers\StringHelper;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

/* @var $this \yii\web\View */
/* @var $content string */

$this->title = Yii::$app->name;

AppAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon" sizes="180x180" href="/fav/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/fav/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/fav/favicon-16x16.png">
    <link rel="manifest" href="/fav/site.webmanifest">
    <link rel="mask-icon" href="/fav/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="sidebar-mini skin-blue fixed">
<?php $this->beginBody() ?>
<div class="wrap">
    <!-- Header-->
    <?= $this->render('_header') ?>
    <!-- Sidebar -->
    <?= $this->render('_sidebar') ?>
    <!-- Right side column. Contains the navbar and content of the page -->
    <div class="content-wrapper">
        <?= Alert::widget(); ?>
        <!-- Content Header (Page header) -->

        <section class="content-header">
            <?php if (\yii\helpers\Url::current() != '/site/index')  : ?>
                <h1>
                    <small><?= $this->title ?></small>
                </h1>
            <?php endif; ?>
            <?php if (!isset($this->params['hideBreadcrumbs']) || $this->params['hideBreadcrumbs'] !== false) : ?>
            <?= Breadcrumbs::widget([
                'links' => StringHelper::limitBreadcrumbsCharsets($this->params['breadcrumbs'] ?? []),
            ]) ?>
            <?php endif; ?>
        </section>

        <!-- Main content -->
        <section class="content">

            <?= $content ?>
        </section>
        <!-- /.content -->
    </div>

    <div class="modal modal-hidden fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="modalMeta">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            </div>
        </div>
    </div>
    <div class="popup" data-popup-id="remote" tabindex="-1" role="dialog">
        <div class="popup__container">
            <div class="popup__close"><span></span><span></span></div>
            <div class="popup__content"></div>
        </div>
    </div>
    <!-- /.content-wrapper -->

</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
