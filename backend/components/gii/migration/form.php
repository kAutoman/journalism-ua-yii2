<?php
/* @var $this yii\web\View */
use backend\components\gii\assets\GiiAsset;
use backend\components\gii\migration\Field;
use backend\components\gii\migration\ForeignKey;
use backend\components\gii\migration\Generator;
use metalguardian\formBuilder\ActiveFormBuilder;
use unclead\multipleinput\MultipleInput;
use yii\helpers\Html;

/* @var $form yii\widgets\ActiveForm */
/* @var $generator Generator */

$asset = GiiAsset::register($this);

echo $form->field($generator, 'tableName');
echo $form->field($generator, 'moduleId');
echo $form->field($generator, 'ns');
echo $form->field($generator, 'modelClass');
echo $form->field($generator, 'controllerClass');
echo $form->field($generator, 'migrationName');
echo Html::hiddenInput('migration-name-beginning', 'm' . gmdate('ymd_His') . '_create_');
echo $form->field($generator, 'isSecondStep')->checkbox();
echo $form->field($generator, 'fields')->widget(MultipleInput::class, [
    'id' => 'migration-gen-fields',
    'sortable' => true,
    'min'               => 1, // should be at least 1 row
    'addButtonPosition' => MultipleInput::POS_HEADER, // show add button in the header
    'columns' => [
        [
            'name'  => 'name',
            'type'  => ActiveFormBuilder::INPUT_TEXT,
            'title' => 'Field name',
        ],
        [
            'name'  => 'type',
            'type'  => ActiveFormBuilder::INPUT_DROPDOWN_LIST,
            'items' => Field::$types,
            'title' => 'Field type',
        ],
        [
            'name'  => 'params',
            'type'  => ActiveFormBuilder::INPUT_TEXT,
            'title' => 'Type params',
        ],
        [
            'name'  => 'isNotNull',
            'type'  => ActiveFormBuilder::INPUT_CHECKBOX,
            'title' => 'Not NULL',
        ],
        [
            'name'  => 'defaultValue',
            'type'  => ActiveFormBuilder::INPUT_TEXT,
            'title' => 'Default value',
        ],
        [
            'name'  => 'isUnsigned',
            'type'  => ActiveFormBuilder::INPUT_CHECKBOX,
            'title' => 'Unsigned',
        ],
        [
            'name'  => 'isUnique',
            'type'  => ActiveFormBuilder::INPUT_CHECKBOX,
            'title' => 'Unique',
        ],
        [
            'name'  => 'isIndex',
            'type'  => ActiveFormBuilder::INPUT_CHECKBOX,
            'title' => 'Index',
        ],
        [
            'name'  => 'comment',
            'type'  => ActiveFormBuilder::INPUT_TEXT,
            'title' => 'Comment',
        ],
        [
            'name'  => 'isLang',
            'type'  => ActiveFormBuilder::INPUT_CHECKBOX,
            'title' => 'Lang',
        ],
    ]
]);
echo $form->field($generator, 'foreignKeys')->widget(MultipleInput::class, [
    'id' => 'migration-gen-fks',
    'min' => 0, // should be at least 1 row
    'addButtonPosition' => MultipleInput::POS_HEADER, // show add button in the header
    'columns' => [
        [
            'name'  => 'fieldName',
            'type'  => ActiveFormBuilder::INPUT_TEXT,
            'title' => 'Field in this table',
        ],
        [
            'name'  => 'relTableName',
            'type'  => ActiveFormBuilder::INPUT_TEXT,
            'title' => 'Related table name',
        ],
        [
            'name'  => 'relTableFieldName',
            'type'  => ActiveFormBuilder::INPUT_TEXT,
            'title' => 'Related field',
        ],
        [
            'name'  => 'delete',
            'type'  => ActiveFormBuilder::INPUT_DROPDOWN_LIST,
            'items' => ForeignKey::$updateDeleteActionTypes,
            'title' => 'Delete',
        ],
        [
            'name'  => 'update',
            'type'  => ActiveFormBuilder::INPUT_DROPDOWN_LIST,
            'items' => ForeignKey::$updateDeleteActionTypes,
            'title' => 'Update',
        ],
    ]
]);
echo $form->field($generator, 'imageUploaders')->widget(MultipleInput::class, [
    'id' => 'migration-gen-images',
    'min'               => 0, // should be at least 1 row
    'addButtonPosition' => MultipleInput::POS_HEADER, // show add button in the header
    'columns' => [
        [
            'name'  => 'attributeLabel',
            'type'  => ActiveFormBuilder::INPUT_TEXT,
            'title' => 'attributeLabel',
        ],
        [
            'name'  => 'attribute',
            'type'  => ActiveFormBuilder::INPUT_TEXT,
            'title' => 'attribute',
        ],
        [
            'name'  => 'aspectRatio',
            'type'  => ActiveFormBuilder::INPUT_TEXT,
            'title' => 'aspectRatio',
        ],
        [
            'name'  => 'multiple',
            'type'  => ActiveFormBuilder::INPUT_CHECKBOX,
            'title' => 'multiple',
        ],
    ]
]);
echo $form->field($generator, 'isSeo')->checkbox();
echo $form->field($generator, 'enableAjaxValidation')->checkbox();
echo $form->field($generator, 'useTablePrefix')->checkbox();
echo $form->field($generator, 'generateLabelsFromComments')->checkbox();
echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'messageCategory');
