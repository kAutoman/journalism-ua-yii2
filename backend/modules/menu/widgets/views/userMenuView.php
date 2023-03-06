<?php
/**
 * @var $items array
 */

use yii\helpers\Html;

?>

<?php foreach ($items as $topItem): ?>

    <ul class="nav-header pull-right">
        <li>
            <div class="btn-group">
                <button class="btn btn-default  dropdown-toggle" data-toggle="dropdown" type="button">
                    <?php $topIcon = $topItem['icon'] ?? ''; ?>
                    <i class="si si-<?= $topIcon; ?>"></i> <?= $topItem['label'] ?>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right">

                    <?php foreach ($topItem['items'] as $item) : ?>
                        <li>
                            <?php
                            $icon = isset($item['icon']) ? Html::tag('i', ['class' => "si si-{$item['icon']}"]) : '';
                            $linkOptions = $item['linkOptions'] ?? [];
                            ?>
                            <?= Html::a("{$icon} {$item['label']}", $item['url'][0], $linkOptions);?>
                        </li>
                    <?php endforeach;?>

                </ul>
            </div>
        </li>

    </ul>
<?php endforeach; ?>
