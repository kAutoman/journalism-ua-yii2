<?php

namespace common\modules\builder\blocks\article;

use Yii;
use backend\components\FormBuilder;
use common\modules\builder\models\BuilderModel;
use yii\helpers\Url;

/**
 * Class ArticleListBlock
 * @package common\modules\builder\blocks\article
 */
class ArticleListBlock extends BuilderModel
{
    /**
     * @var string
     */
//    public $title;

    /**
     * List of ALL attributes corresponding to $this model
     *
     * @return array
     */
    public function getBuilderAttributes(): array
    {
        return [
//            'title',
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
//            [['title'], 'required'],
//            [['title'], 'string', 'max' => MAX_TEXT],
        ];
    }

    /**
     * Title for current builder block
     *
     * @return string
     */
    public static function getTitle(): string
    {
        return Yii::t('back/builder', 'Article list block');
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
//            'title' => Yii::t('back/builder', 'Title'),
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
            'list' => Url::toRoute(['/article/article/list']),
        ];
    }
}

