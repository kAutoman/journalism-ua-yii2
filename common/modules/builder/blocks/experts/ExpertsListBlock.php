<?php

namespace common\modules\builder\blocks\experts;

use common\models\Expert;
use common\modules\builder\models\BuilderModel;

/**
 * Class ExpertsListBlock
 * @package common\modules\builder\blocks
 */
class ExpertsListBlock extends BuilderModel
{
    /**
     * @var string
     */
//    public $title;

    /**
     * @var string
     */
//    public $content;

    /**
     * @var array
     */
    public $items;

    /**
     * List of ALL attributes corresponding to $this model
     *
     * @return array
     */
    public function getBuilderAttributes(): array
    {
        return [
//            'title',
//            'content',
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
//            [['title', 'content'], 'required'],
//            [['title'], 'string', 'max' => MAX_TEXT],
//            [['content'], 'string', 'max' => MAX_EDITOR]
        ];
    }

    /**
     * Title for current builder block
     *
     * @return string
     */
    public static function getTitle(): string
    {
        return bt('Experts page', 'builder');
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
//            'title' => bt('Title', 'builder'),
//            'content' => bt('Content', 'builder'),
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
//            'title' => ['type' => FormBuilder::INPUT_TEXT],
//            'content' => [
//                'type' => FormBuilder::INPUT_WIDGET,
//                'widgetClass' => Editor::class,
//                'widgetOptions' => [
//                    'model' => $this,
//                    'attribute' => 'content'
//                ]
//            ],
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
//            'title' => $this->title,
//            'content' => $this->content,
            'items' => $this->getExpertsItems(),
        ];
    }

    /**
     * @return array
     */
    protected function getExpertsItems(): array
    {
        /** @var Expert[] $models */
        $models = Expert::find()
            ->isPublished()
            ->orderBy(['position' => SORT_ASC])
            ->all();

        return array_map(function (Expert $model) {
            return [
                'name' => $model->name,
                'description' => $model->staff,
                'photo' => formatter()->image($model->photoSrc->file_id ?? 0),
            ];
        }, $models);
    }
}
