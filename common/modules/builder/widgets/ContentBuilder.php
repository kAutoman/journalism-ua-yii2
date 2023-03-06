<?php

namespace common\modules\builder\widgets;

use common\components\model\ActiveRecord;
use common\modules\builder\interfaces\Constructable;
use yii\base\InvalidConfigException;
use yii\base\Widget;

/**
 * Class ContentBuilder
 *
 * @package common\modules\builder\widgets
 */
class ContentBuilder extends Widget
{
    /**
     * Working model with connected builder
     *
     * @var ActiveRecord
     */
    public $model;

    /**
     * Builder attribute with array of builder models
     *
     * @var string
     */
    public $attribute;

    /**
     * Path to view file. Default: `./views`
     *
     * @var string
     */
    public $viewPath;

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        if (!$this->model || !$this->attribute) {
            throw new InvalidConfigException('Properties `model` or `attribute` not set.');
        }

        parent::init();
    }

    /**
     * Render and run builder content
     *
     * @return string
     */
    public function run()
    {
        $builderModels = $this->model->{$this->attribute};
        $output = '';
        /** @var Constructable $model */
        foreach ($builderModels as $model) {
            $output .= $this->render($this->viewPath . $model->getViewFileName(), compact('model'));
        }

        return $output;
    }
}
