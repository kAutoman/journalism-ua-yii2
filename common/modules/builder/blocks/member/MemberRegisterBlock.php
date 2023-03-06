<?php

namespace common\modules\builder\blocks\member;

use common\models\MemberIcon;
use Yii;
use backend\components\FormBuilder;
use common\modules\builder\models\BuilderModel;

/**
 * Class MemberRegisterBlock
 * @package common\modules\builder\blocks\member
 */
class MemberRegisterBlock extends BuilderModel
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $content;

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
            'content',
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
            [['title', 'content', 'button_label', 'button_link'], 'required'],
            [['title'], 'string', 'max' => MAX_TEXT],
            [['content'], 'string', 'max' => MAX_TEXTAREA],
            [
                [
                    'button_label',
                    'button_link',
                ],
                'string',
                'max' => MAX_TEXT
            ],
            ['button_form_enable', 'safe'],
        ];
    }

    /**
     * Title for current builder block
     *
     * @return string
     */
    public static function getTitle(): string
    {
        return Yii::t('back/builder', 'Register member page');
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
            'content' => Yii::t('back/builder', 'Content'),
            'button_label' => Yii::t('back/builder', 'Button label'),
            'button_link' => Yii::t('back/builder', 'Button link'),
            'button_form_enable' => Yii::t('back/builder', 'Button form'),
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
            'content' => ['type' => FormBuilder::INPUT_TEXTAREA],
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
            'content' => $this->content,
            'button_label' => $this->button_label,
            'button_link' => $this->button_link,
            'button_form_enable' => $this->button_form_enable,
            'items' => $this->getIconItems(),
        ];
    }

    /**
     * @return array
     */
    protected function getIconItems(): array
    {
        /** @var MemberIcon[] $models */
        $models = MemberIcon::find()
            ->isPublished()
            ->orderBy(['position' => SORT_ASC])
            ->limit(9)
            ->all();

        return array_map(function (MemberIcon $model) {
            return [
                'icon' => formatter()->image($model->iconSrc->file_id ?? 0),
                'description' => $model->description,
            ];
        }, $models);
    }
}

