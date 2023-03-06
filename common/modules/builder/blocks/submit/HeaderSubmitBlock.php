<?php

namespace common\modules\builder\blocks\submit;

use Yii;
use backend\components\FormBuilder;
use common\modules\builder\models\BuilderModel;

/**
 * Class HeaderSubmitBlock
 * @package common\modules\builder\blocks\submit
 */
class HeaderSubmitBlock extends BuilderModel
{
    /**
     * @var string
     */
    public $label;

    /**
     * List of ALL attributes corresponding to $this model
     *
     * @return array
     */
    public function getBuilderAttributes(): array
    {
        return [
            'label',
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
            [['label'], 'required'],
            [['label'], 'string', 'max' => MAX_TEXT]
        ];
    }

    /**
     * Title for current builder block
     *
     * @return string
     */
    public static function getTitle(): string
    {
        return Yii::t('back/builder', 'Header block');
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
            'label' => Yii::t('back/builder', 'Label'),
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
            'label' => ['type' => FormBuilder::INPUT_TEXT],
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
            'label' => $this->label
        ];
    }
}
