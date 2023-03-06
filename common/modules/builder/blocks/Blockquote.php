<?php

namespace common\modules\builder\blocks;

use Yii;
use common\modules\builder\models\BuilderModel;
use backend\components\FormBuilder;

/**
 * Class Blockquote
 *
 * @property string $content
 *
 * @package common\modules\builder\blocks
 */
class Blockquote extends BuilderModel
{

    /**
     * @var string
     */
    public $content;

    /**
     * List of ALL attributes corresponding to $this model
     *
     * @return array
     */
    public function getBuilderAttributes(): array
    {
        return [
            'content',
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
            [['content'], 'required'],
            [['content'], 'string', 'max' => MAX_TEXTAREA]
        ];
    }

    /**
     * Title for current builder block
     *
     * @return string
     */
    public static function getTitle(): string
    {
        return Yii::t('back/builder', 'Blockquote');
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
            'content' => Yii::t('back/builder', 'Content'),
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
            'content' => ['type' => FormBuilder::INPUT_TEXTAREA],
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
            'content' => $this->content
        ];
    }
}
