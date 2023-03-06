<?php

namespace common\modules\builder\blocks;

use common\models\WinnerList;
use Yii;
use backend\components\FormBuilder;
use common\modules\builder\models\BuilderModel;

/**
 * Class WinnerListBlock
 * @package common\modules\builder\blocks
 */
class WinnerListBlock extends BuilderModel
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
     * List of all file attributes. MUST have the following syntax:
     * ```
     * return [
     *      ...,
     *      `attributeName` => self::ATTRIBUTE_FILE_CONSTANT,
     *      `image` => self::SAVE_ATTRIBUTE_IMAGE,
     *      ...
     * ];
     * ```
     *
     * @return array
     */
    public function getUploadAttributes(): array
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
        return Yii::t('back/builder', 'Winner list');
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
            'items' => $this->getWinnersList(),
        ];
    }

    /**
     * @return array
     */
    public function getWinnersList(): array
    {
        /** @var WinnerList[] $models */
        $models = WinnerList::find()
            ->isPublished()
            ->orderBy(['position' => SORT_ASC])
            ->all();

        return array_map(function (WinnerList $model) {
            $image = ($model->imageSrc) ? (int)$model->imageSrc->file_id : null;
            $file = ($model->fileSrc) ? (int)$model->fileSrc->file_id : null;

            return [
                'category' => $model->memberItem->label ?? null,
                'name' => $model->name,
                'publication_label' => $model->publication_label,
                'publication_link' => $model->publication_link,
                'image' => formatter()->image($image),
                'file' => formatter()->file($file),
            ];
        }, $models);
    }
}
