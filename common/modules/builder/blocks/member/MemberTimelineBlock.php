<?php

namespace common\modules\builder\blocks\member;

use Yii;
use backend\components\FormBuilder;
use common\modules\builder\models\BuilderModel;
use common\models\MemberTimeline as MemberTimelineModel;

/**
 * Class MemberTimelineBlock
 * @package common\modules\builder\blocks\member
 */
class MemberTimelineBlock extends BuilderModel
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
     * List of ALL attributes corresponding to $this model
     *
     * @return array
     */
    public function getBuilderAttributes(): array
    {
        return [
            'title',
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
            [['title'], 'required'],
            [['title'], 'string', 'max' => MAX_TEXT],
            [['content'], 'string', 'max' => MAX_TEXTAREA],
        ];
    }

    /**
     * Title for current builder block
     *
     * @return string
     */
    public static function getTitle(): string
    {
        return Yii::t('back/builder', 'Timeline member page');
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
            'items' => $this->getTimelinesItems(),
        ];
    }

    /**
     * @return array
     */
    protected function getTimelinesItems(): array
    {
        /** @var MemberTimelineModel[] $models */
        $models = MemberTimelineModel::find()
            ->isPublished()
            ->orderBy(['position' => SORT_ASC])
            ->limit(9)
            ->all();

        return array_map(function (MemberTimelineModel $model) {
            return [
                'label' => $model->label,
                'content' => $model->content,
                'date' => $model->date,
            ];
        }, $models);
    }
}

