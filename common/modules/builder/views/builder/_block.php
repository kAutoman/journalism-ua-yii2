<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use backend\components\FormBuilder;
use common\modules\builder\models\BuilderModel;
use common\modules\builder\widgets\DummyFormBuilder;
use yii\helpers\Json;

/**
 * @var BuilderModel $builderModel
 * @var int $key
 * @var string $targetAttribute
 */


?>
<?php $form = DummyFormBuilder::begin([
    'id' => "form-{$key}",
]); ?>
<div class="builder-row builder-item panel" data-key-idx="<?= $key ?>">

    <div class="row-header clearfix">

        <?php if ($builderModel->getIsSortable()) : ?>
            <?= Html::a("<i class='glyphicon glyphicon-move'></i>", "#", [
            'class' => 'sub-btn content-row-trigger-sort'
            ]); ?>
        <?php endif; ?>

        <span><b><?= $builderModel->getTitle(); ?></b></span>

        <?php if  ($builderModel->getIsRemovable()) : ?>
            <?= Html::a("<i class='glyphicon glyphicon-trash'></i>",
                '#',
                [
                    'class' => $builderModel->getIsNewRecord() ? 'sub-btn content-row-trash' : 'sub-btn content-row-trash delete-form-builder',
                    'data' => ['id' =>  $builderModel->id]
                ]
            ); ?>
        <?php endif; ?>

        <?= Html::a("<i class='glyphicon glyphicon-resize-vertical'></i>", "#",
            ['class' => 'sub-btn content-row-sort js-collapse']); ?>


        <?= Html::a("<i class='fa fa-cog'></i>", '#', [
            'class' => 'sub-btn block-settings',
            'data' => ['toggle' => 'modal', 'target' => '#settings-' . $key],
        ]); ?>


        <div class="sub-btn content-row-tag-level">
            <?= $form->field($builderModel, "[{$key}]tag_level", ['template' => "
            <div class='row'>
                <div class='pull-right'>{input}</div>
                <div class='tag-level-label pull-right text-right'>{label}</div>    
            </div>
            <div class='row'>{hint}{error}</div>
            "])
            ->dropDownList($builderModel->tagLevelRange, ['class' => 'tag-level-select']);
            ?>
        </div>
    </div>

    <br>

    <div class="row-content collapse-content active <?= $builderModel->published ? '' : 'disabled'; ?>">
        <a class="overlay">
            <i class="fa fa-eye-slash"><br><?= bt('Disabled', 'builder'); ?></i>
        </a>
        <?php echo $form->renderField($builderModel, "[{$key}]id",
            ['type' => FormBuilder::INPUT_HIDDEN, 'label' => false]); ?>
        <?php foreach ($builderModel->getFormConfig() as $attribute => $config) {
            $config['label'] = ArrayHelper::getValue($builderModel->getAttributeLabels(), $attribute, false);

            echo $form->renderField($builderModel, "[{$key}]{$attribute}", $config);
        }; ?>
    </div>

    <div class="modal fade" id="settings-<?= $key; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?= $builderModel::getTitle(); ?></h4>
                </div>
                <div class="modal-body">
                    <?= $form->field($builderModel, "[{$key}]component_name")->textInput(); ?>
                    <?= $form->field($builderModel, "[{$key}]published")->checkbox(); ?>

                    <?php if (!$builderModel->getIsNewRecord()) : ?>
                    <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#code-example-<?= $key; ?>" aria-expanded="false">
                        <?= bt('Response', 'builder'); ?>
                    </button>
                    <div class="collapse" id="code-example-<?= $key; ?>">
                        <pre>
                            <code class="json">
                                <?php
                                    try {
                                        $attributes = $builderModel->getApiAttributes();
                                    } catch (Throwable $e) {
                                        $attributes = [];
                                    }

                                    $response = [
                                        'id' => $builderModel->getShortName(),
                                        'level' => $builderModel->tag_level ?? null,
                                        'attributes' => $attributes
                                    ];
                                    $formattedJson = Json::encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                                    echo highlight_string($formattedJson, true);
                                ?>
                            </code>
                        </pre>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= bt('Close'); ?></button>
                </div>
            </div>
        </div>
    </div>

</div>
<?php DummyFormBuilder::end(); ?>
