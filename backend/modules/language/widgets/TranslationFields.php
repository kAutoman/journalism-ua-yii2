<?php

namespace backend\modules\language\widgets;

use backend\modules\language\models\SourceMessage;
use common\helpers\LanguageHelper;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * Class TranslationFields
 *
 * @package backend\modules\language\widgets
 */
class TranslationFields extends Widget
{
    /**
     * @var SourceMessage
     */
    public $model;

    public $attribute;

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        if (!$this->model || !$this->attribute) {
            throw new InvalidConfigException('Properties `model` AND `attribute` are mandatory');
        }

        return parent::init();
    }

    /**
     * @return string|void
     * @throws InvalidConfigException
     */
    public function run()
    {
        $this->model->{$this->attribute} = [];
        foreach ($this->model->messages as $message) {
            $this->model->{$this->attribute}[$message->language] = $message->translation;
        }

        $languages = LanguageHelper::getApplicationLanguages();
        foreach ($languages as $language) {
            echo '<br>';
            echo Html::label($language, "{$this->model->formName()}[{$language}][$this->attribute]");
            echo Html::activeTextarea($this->model, $this->attribute . "[{$language}]", [
                'class' => 'form-control'
            ]);
        }
    }
}
