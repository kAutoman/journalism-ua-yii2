<?php

namespace common\modules\builder\widgets;

use common\modules\builder\models\BuilderModel;
use yii\base\Widget;
use common\components\model\ActiveRecord;
use common\modules\builder\assets\BuilderAssets;
use common\modules\builder\behaviors\BuilderBehavior;

/**
 * Class BuilderForm
 *
 * Example:
 * ```
 * Yii::t('back/app', 'Builder') => [
 *     'builderContent' => [
 *         'type' => FormBuilder::INPUT_WIDGET,
 *         'widgetClass' => BuilderForm::class,
 *         'label' => false,
 *          options' => [
 *              'model' => $this,
 *              'attribute' => 'builderContent'
 *         ]
 *     ],
 * ],
 * ```
 *
 * @package common\builder\widgets
 */
class BuilderForm extends Widget
{
    /**
     * @var ActiveRecord|BuilderBehavior
     */
    public $model;

    /**
     * @var string
     */
    public $attribute;

    /**
     * @var string
     */
    private $blockViewPath = '@common/modules/builder/views/builder/_block';

    /**
     * @inheritdoc
     * @return string
     */
    public function run()
    {
        BuilderAssets::register($this->getView());

        return $this->render('builder-form', [
            'model' => $this->model,
            'targetAttribute' => $this->attribute,
            'data' => $this->getBuilderModels(),
            'blocks' => $this->renderExistBlocks()
        ]);
    }

    /**
     * Generate output of existing builder blocks.
     *
     * @return string
     */
    public function renderExistBlocks(): string
    {
        $blocks = $this->model->{$this->attribute};

        $html = '';
        if (!empty($blocks)) {
            foreach ($blocks as $key => $block) {
                $html .= $this->render($this->blockViewPath, [
                    'model' => $this->model,
                    'builderModel' => $block,
                    'key' => $key,
                    'open' => false
                ]);
            }
        }

        return $html;
    }

    /**
     * Get array of builder models. Used in dropdown selector.
     *
     * @return array
     */
    private function getBuilderModels(): array
    {
       return $this->model->getFormattedModels();
    }
}
