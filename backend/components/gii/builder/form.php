<?php

use backend\components\FormBuilder;
use backend\components\gii\assets\GiiAsset;
use backend\components\gii\builder\Generator;
use unclead\multipleinput\MultipleInput;

/**
 * @var $this yii\web\View
 * @var $form yii\widgets\ActiveForm
 * @var $generator Generator
 */

$asset = GiiAsset::register($this);

echo $form->field($generator, 'className');
echo $form->field($generator, 'ns');
echo $form->field($generator, 'title')->hint('Builder block title');

echo $form->field($generator, 'fields')->widget(MultipleInput::class, [
    'id' => 'builder-gen-fields',
    'sortable' => true,
    'min'               => 1, // should be at least 1 row
    'addButtonPosition' => MultipleInput::POS_HEADER, // show add button in the header
    'enableError' => true,
    'columns' => [
        [
            'name'  => 'name',
            'type'  => FormBuilder::INPUT_TEXT,
            'title' => 'Field name',
        ],
        [
            'name'  => 'type',
            'type'  => FormBuilder::INPUT_DROPDOWN_LIST,
            'items' => $generator::$types,
            'title' => 'Field type',
        ],
        [
            'name'  => 'isRequired',
            'type'  => FormBuilder::INPUT_CHECKBOX,
            'title' => 'Required',
        ],
        [
            'name'  => 'label',
            'type'  => FormBuilder::INPUT_TEXT,
            'title' => 'Field label',
        ],
    ]
]);

echo $form->field($generator, 'baseClass');
echo $form->field($generator, 'viewFileName');
//echo $form->field($generator, 'generateLabelsFromComments')->checkbox();
echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'messageCategory');
