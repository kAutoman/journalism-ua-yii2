<?php

use backend\widgets\Menu;

?>
<aside class="main-sidebar">
    <section class="sidebar">
        <?= Menu::widget([
            'options' => ['class' => 'sidebar-menu', 'data-widget' => 'tree'],
            'items' => require(Yii::getAlias('@backend') . '/config/menu-items.php')
        ]); ?>
    </section>
</aside>
