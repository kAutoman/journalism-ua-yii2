<?php

namespace common\modules\builder\blocks\nominations;

use common\models\MemberItem;
use Yii;
use backend\components\FormBuilder;
use common\modules\builder\models\BuilderModel;

/**
 * Class ListNominationBlock
 * @package common\modules\builder\blocks\nominations
 */
class ListNominationBlock extends BuilderModel
{
    /**
     * List of ALL attributes corresponding to $this model
     *
     * @return array
     */
    public function getBuilderAttributes(): array
    {
        return [
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
        ];
    }

    /**
     * Title for current builder block
     *
     * @return string
     */
    public static function getTitle(): string
    {
        return Yii::t('back/builder', 'Nomination list block');
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
            'items' => $this->getNominationItems(),
        ];
    }

    /**
     * @return array
     */
    protected function getNominationItems(): array
    {
        /** @var MemberItem[] $models */
        $models = MemberItem::find()
            ->isPublished()
            ->orderBy([
                'position' => SORT_ASC
            ])
            ->all();

        return array_map(function (MemberItem $model) {
            return [
                'label' => $model->label,
                'hash' => 'member_' . $model->id,
                'content' => $model->content,
            ];
        }, $models);
    }
}
