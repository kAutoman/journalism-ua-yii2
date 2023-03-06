<?php

namespace common\modules\builder\blocks;

use Yii;
use common\modules\builder\models\BuilderModel;
use backend\components\FormBuilder;
use backend\widgets\Editor;
use backend\modules\imagesUpload\widgets\imagesUpload\ImageUpload;

/**
 * Class LeftRightTextArea
 *
 * @property string $left_content
 * @property string $right_content
 *
 * @package common\modules\builder\blocks
 */
class LeftRightTextArea extends BuilderModel
{

    /**
     * @var string
     */
    public $left_content;

    /**
     * @var string
     */
    public $right_content;

    /**
     * List of ALL attributes corresponding to $this model
     *
     * @return array
     */
    public function getBuilderAttributes(): array
    {
        return [
            'left_content',
            'right_content',
        ];
    }


    /**
     * Returns the validation rules for attributes.
     * The same as default {{rules()}} method.
     *
     * @return array
     */
    public function validationRules(): array
    {
        return [
            [['left_content', 'right_content'], 'required'],
            [['left_content', 'right_content'], 'string', 'max' => MAX_TEXTAREA]
        ];
    }

    /**
     * Title for current builder block
     *
     * @return string
     */
    public static function getTitle(): string
    {
        return Yii::t('back/builder', 'Left & right text area');
    }

    /**
     * Array of properties labels
     *
     * @return array
     * @see `attributeLabels()` in {ActiveRecord}
     */
    public function getAttributeLabels(): array
    {
        return [
            'left_content' => Yii::t('back/builder', 'Left content'),
            'right_content' => Yii::t('back/builder', 'Right content'),
        ];
    }

    /**
     * Form data config. Passes to {FormBuilder} to generate form fields
     *
     * @return array
     * @see \common\modules\builder\widgets\DummyFormBuilder
     * @throws \Exception
     */
    public function getFormConfig(): array
    {
        return [
            'left_content' => ['type' => FormBuilder::INPUT_TEXTAREA],
            'right_content' => ['type' => FormBuilder::INPUT_TEXTAREA],
        ];
    }

    /**
     * Block attributes for API response.
     *
     * @return array
     */
    public function getApiAttributes(): array
    {
        return [
            'left_content' => $this->left_content,
            'right_content' => $this->right_content
        ];
    }
}
