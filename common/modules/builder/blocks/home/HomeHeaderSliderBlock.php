<?php

namespace common\modules\builder\blocks\home;

use common\models\HomeHeaderSlider as HomeHeaderSliderModel;
use Yii;
use common\modules\builder\models\BuilderModel;

/**
 * Class HomeHeaderSliderBlock
 * @package common\modules\builder\blocks\home
 */
class HomeHeaderSliderBlock extends BuilderModel
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
        return Yii::t('back/builder', 'Header slider');
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
            'items' => $this->getImagesList(),
        ];
    }

    /**
     * @return array
     */
    public function getImagesList(): array
    {
        /** @var HomeHeaderSliderModel[] $models */
        $models = HomeHeaderSliderModel::find()
            ->isPublished()
            ->orderBy(['position' => SORT_ASC])
            ->limit(8)
            ->all();

        return array_map(function (HomeHeaderSliderModel $model) {
            return [
                'label' => $model->label,
                'content' => $model->content,
                'button_label' => $model->button_label,
                'button_src' => $model->button_src,
                'button_form_enable' => $model->button_form_enable,
                'image' => formatter()->image($model->imageSrc->file_id ?? 0),
            ];
        }, $models);
    }
}
