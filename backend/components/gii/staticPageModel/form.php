<?php

use backend\components\gii\assets\GiiAsset;
use backend\modules\configuration\models\Configuration;
use metalguardian\formBuilder\ActiveFormBuilder;
use unclead\multipleinput\MultipleInput;

/**
 * @var $form yii\widgets\ActiveForm
 * @var $generator yii\gii\generators\form\Generator
 * @var $this yii\web\View
 */

$asset = GiiAsset::register($this);

echo $form->field($generator, 'modelClassName');

echo $form->field($generator, 'moduleId');

echo $form->field($generator, 'ns');

echo $form->field($generator, 'controllerClass');

echo $form->field($generator, 'title');

echo $form->field($generator, 'keys')->widget(MultipleInput::class, [
    'min' => 1,
    'sortable' => true,
    'addButtonPosition' => MultipleInput::POS_HEADER,
    'columns' => [
        [
            'name'  => 'id',
            'type'  => ActiveFormBuilder::INPUT_TEXT,
            'title' => 'Key',
        ],
        [
            'name'  => 'type',
            'type'  => ActiveFormBuilder::INPUT_DROPDOWN_LIST,
            'items' => Configuration::getTypesList(),
            'title' => 'Field type',
        ],
        [
            'name'  => 'description',
            'type'  => ActiveFormBuilder::INPUT_TEXT,
            'title' => 'Field description',
        ],
        [
            'name'  => 'hint',
            'type'  => ActiveFormBuilder::INPUT_TEXT,
            'title' => 'Field hint',
        ],
        [
            'name'  => 'isRequired',
            'type'  => ActiveFormBuilder::INPUT_CHECKBOX,
            'title' => 'Is Required',
        ],
        [
            'name'  => 'rule',
            'type'  => ActiveFormBuilder::INPUT_TEXT,
            'title' => 'Rule',
        ],
        [
            'name'  => 'isTranslatable',
            'type'  => ActiveFormBuilder::INPUT_CHECKBOX,
            'title' => 'Is Translatable',
        ],
    ]
]);

echo $form->field($generator, 'isSeo')->checkbox();

echo $form->field($generator, 'relationsForRelatedFormWidget')->widget(MultipleInput::class, [
    'min' => 0,
    'addButtonPosition' => MultipleInput::POS_HEADER,
    'columns' => [
        [
            'name'  => 'relationName',
            'type'  => ActiveFormBuilder::INPUT_TEXT,
            'title' => 'Relation name',
        ],
        [
            'name'  => 'tabName',
            'type'  => ActiveFormBuilder::INPUT_TEXT,
            'title' => 'Tab name',
        ],
    ]
]);

echo $form->field($generator, 'enableI18N')->checkbox();

echo $form->field($generator, 'messageCategory');
