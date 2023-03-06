<?php

namespace common\modules\builder\blocks\participants;

use Yii;
use backend\components\FormBuilder;
use common\models\MemberItem;
use common\modules\builder\models\BuilderModel;

/**
 * Class ParticipantsNominationBlock
 * @package common\modules\builder\blocks\participants
 */
class ParticipantsNominationBlock extends BuilderModel
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $button_label;

    /**
     * @var string
     */
    public $button_link;

    /**
     * @var string
     */
    public $button_form_enable;

    /**
     * List of ALL attributes corresponding to $this model
     *
     * @return array
     */
    public function getBuilderAttributes(): array
    {
        return [
            'title',
            'button_label',
            'button_link',
            'button_form_enable',
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
            [['title'], 'required'],
            [['title'], 'string', 'max' => MAX_TEXT],
            [['button_label'], 'string', 'max' => MAX_TEXT],
            [['button_link'], 'string', 'max' => MAX_TEXT],
            [['button_form_enable'], 'string', 'max' => MAX_TEXT],
        ];
    }

    /**
     * Title for current builder block
     *
     * @return string
     */
    public static function getTitle(): string
    {
        return Yii::t('back/builder', 'Nomination block');
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
            'button_label' => Yii::t('back/builder', 'Button label'),
            'button_link' => Yii::t('back/builder', 'Button link'),
            'button_form_enable' => Yii::t('back/builder', 'Button form enable'),
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
            'button_label' => ['type' => FormBuilder::INPUT_TEXT],
            'button_link' => ['type' => FormBuilder::INPUT_TEXT],
            'button_form_enable' => ['type' => FormBuilder::INPUT_CHECKBOX],
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
            'title' => $this->title,
            'items' => $this->getItemsList(),
            'button_label' => $this->button_label,
            'button_link' => $this->button_link,
            'button_form_enable' => $this->button_form_enable,
        ];
    }

    /**
     * @return array
     */
    protected function getItemsList(): array
    {
        /** @var MemberItem[] $models */
        $models = MemberItem::find()
            ->isPublished()
            ->orderBy(['position' => SORT_ASC])
            ->all();

        return array_map(function (MemberItem $model) {
            return [
                'label' => $model->label,
                'content' => $model->content,
            ];
        }, $models);
    }
}
