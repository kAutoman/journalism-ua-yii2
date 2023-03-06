<?php

namespace common\modules\builder\blocks;

use Yii;
use common\modules\builder\models\BuilderModel;
use backend\components\FormBuilder;

/**
 * Class HeadingTwoTextArea
 *
 * @property string $title
 * @property string $left_content
 * @property string $right_content
 *
 * @package common\modules\builder\blocks
 */
class HeadingTwoTextArea extends BuilderModel
{

    /**
     * @var string
     */
    public $title;

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
            'title',
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
            [['title', 'left_content', 'right_content'], 'required'],
            [['title'], 'string', 'max' => MAX_TEXT],
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
        return Yii::t('back/builder', 'Heading with 2 text area');
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
            'title' => Yii::t('back/builder', 'Title'),
            'left_content' => Yii::t('back/builder', 'Left content'),
            'right_content' => Yii::t('back/builder', 'Right content'),
        ];
    }

    /**
     * Form data config. Passes to {FormBuilder} to generate form fields
     *
     * @return array
     * @throws \Exception
     * @see \common\modules\builder\widgets\DummyFormBuilder
     */
    public function getFormConfig(): array
    {
        return [
            'title' => ['type' => FormBuilder::INPUT_TEXT],
            'left_content' => ['type' => FormBuilder::INPUT_TEXTAREA],
            'right_content' => ['type' => FormBuilder::INPUT_TEXTAREA],
        ];
    }

    public function getApiAttributes(): array
    {
        return [
            'title' => $this->title,
            'left_content' => $this->left_content,
            'right_content' => $this->right_content,
        ];
    }
}
