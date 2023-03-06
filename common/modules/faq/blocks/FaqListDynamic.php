<?php

namespace common\modules\faq\blocks;

use common\modules\faq\models\Faq;
use common\modules\faq\models\FaqCategory;
use Yii;
use common\modules\builder\models\BuilderModel;
use backend\components\FormBuilder;

/**
 * Class FaqListDynamic
 *
 * @property string $title
 * @property int $category
 *
 * @package common\modules\faq\blocks
 */
class FaqListDynamic extends BuilderModel
{
    /**
     * @var string
     */
    public $title;
    /**
     * @var int
     */
    public $category;

    /**
     * List of ALL attributes corresponding to $this model
     *
     * @return array
     */
    public function getBuilderAttributes(): array
    {
        return [
            'title',
            'category',
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
            [['category'], 'integer'],
            [['category', 'title'], 'required'],
            [['title'], 'string', 'max' => 100],
        ];
    }

    /**
     * Title for current builder block
     *
     * @return string
     */
    public static function getTitle(): string
    {
        return Yii::t('back/faq', 'FAQ list (dynamic)');
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
            'category' => Yii::t('back/faq', 'Category'),
            'title' => Yii::t('back/faq', 'Block title'),
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
            'title' => [
                'type' => FormBuilder::INPUT_TEXT,
            ],
            'category' => [
                'type' => FormBuilder::INPUT_DROPDOWN_LIST,
                'items' => FaqCategory::getList()
            ],
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
            'dynamicData' => Faq::getItemsListUrl(['category' => $this->category]),
        ];
    }
}
