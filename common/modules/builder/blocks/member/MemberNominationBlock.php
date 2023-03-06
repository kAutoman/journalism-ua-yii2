<?php

namespace common\modules\builder\blocks\member;

use Yii;
use backend\components\FormBuilder;
use common\modules\builder\models\BuilderModel;
use common\models\MemberItem;

/**
 * Class MemberNominationBlock
 * @package common\modules\builder\blocks\member
 */
class MemberNominationBlock extends BuilderModel
{
    /**
     * @var string
     */
    public $title;

    /**
     * List of ALL attributes corresponding to $this model
     *
     * @return array
     */
    public function getBuilderAttributes(): array
    {
        return [
            'title',
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
        ];
    }

    /**
     * Title for current builder block
     *
     * @return string
     */
    public static function getTitle(): string
    {
        return Yii::t('back/builder', 'Nomination member page');
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
            'items' => $this->getNominationsItems(),
        ];
    }

    /**
     * @return array
     */
    protected function getNominationsItems(): array
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

