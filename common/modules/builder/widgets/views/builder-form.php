<?php

use backend\components\FormBuilder;
use common\components\model\ActiveRecord;
use common\helpers\LanguageHelper;
use common\modules\builder\behaviors\BuilderBehavior;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var $this View
 * @var $data array
 * @var $blocks string
 * @var $model ActiveRecord|BuilderBehavior
 * @var $targetAttribute string
 */

$currentEditLang = LanguageHelper::getEditLanguage();
$defaultLang = LanguageHelper::getDefaultLanguage()->code;
?>
    <?php if ($model->getMode() === BuilderBehavior::MODE_TYPE_DYNAMIC) : ?>
    <?= Select2::widget([
        'data' => $data,
        'name' => 'builder-selector',
        'size' => 'md',
        'showToggleAll' => false,
        'options' => [
            'class' => 'add-content-builder-list',
            'placeholder' => Yii::t('back/app', 'Select...'),
        ],
        'addon' => [
            'append' => [
                'content' => Html::button('+', [
                    'class' => 'btn btn-success add-content-builder',
                    'data' => [
                        'href' => '/builder/builder/add',
                        'params' => [
                            Yii::$app->getRequest()->csrfParam => Yii::$app->getRequest()->csrfToken,
                            'targetClass' => get_class($model), // ?
                            'id' => $model->getPrimaryKey(),
                            'targetAttribute' => $targetAttribute,
                        ],
                        'key' => $model->$targetAttribute !== null ? count($model->$targetAttribute) : 0,
                    ]
                ]),
                'asButton' => true
            ]
        ]
    ]); ?>
    <?php endif; ?>
<hr>

<div class="builder-container ui-sortable-handle">
    <div class="text-center">
        <?php
        if (empty($blocks) && $currentEditLang !== $defaultLang) {
            echo Html::a(
                Yii::t('back/builder', 'Clone from {lang}', ['lang' => $defaultLang]),
                [
                    '/builder/builder/clone',
                    'targetClass' => (new ReflectionClass($model))->getShortName(),
                    'id' => $model->getPrimaryKey(),
                    'attribute' => $targetAttribute,
                    'lang' => $currentEditLang
                ],
                ['class' => ['btn btn-info btn-lg']]
            );
        }
        ?>

    </div>

    <?= $blocks; ?>
</div>



