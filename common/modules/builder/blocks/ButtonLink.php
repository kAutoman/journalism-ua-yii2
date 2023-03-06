<?php

namespace common\modules\builder\blocks;

use Yii;
use common\modules\builder\models\BuilderModel;
use backend\components\FormBuilder;

/**
 * Class Button
 *
 * @property string $label
 * @property string $link
 *
 * @package common\modules\builder\blocks
 */
class ButtonLink extends BuilderModel
{

    /**
     * @var string
     */
    public $label;

    /**
     * @var string
     */
    public $link;

    /**
     * List of ALL attributes corresponding to $this model
     *
     * @return array
     */
    public function getBuilderAttributes(): array
    {
        return [
            'label',
            'link',
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
            [['label', 'link'], 'required'],
            [['label', 'link'], 'string', 'max' => MAX_TEXT],
            [['link'], 'url']
        ];
    }

    /**
     * Title for current builder block
     *
     * @return string
     */
    public static function getTitle(): string
    {
        return Yii::t('back/builder', 'Button link');
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
            'link' => Yii::t('back/builder', 'link'),
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
            'label' => ['type' => FormBuilder::INPUT_TEXT],
            'link' => ['type' => FormBuilder::INPUT_TEXT],
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
            'button' => app()->getFormatter()->link($this->label, $this->link),
        ];
    }
}
