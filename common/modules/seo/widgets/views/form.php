<?php

use yii\helpers\Html;
use backend\components\FormBuilder;
use common\components\model\ActiveRecord;
use common\modules\seo\models\MetaTagsContent;
use common\modules\seo\behaviors\MetaTagsBehavior;

/**
 * @var ActiveRecord $model
 * @var MetaTagsContent[] $metaTags
 * @var FormBuilder $form
 * @var array $formConfig
 * @var MetaTagsBehavior $behavior
 */

echo Html::beginTag('div', ['class' => 'form-group']);

$content = null;
foreach ($metaTags as $attribute => $model) {
    $settings = $model->getFormConfig()[$attribute];

    if ($behavior->defaultTitleAttribute && in_array($attribute, $behavior->getTitleAttributes()) && empty($model->{$attribute})) {
        $settings['options']['value'] = $behavior->owner->{$behavior->defaultTitleAttribute};
    }
    if ($behavior->defaultDescriptionAttribute && in_array($attribute, $behavior->getDescriptionAttributes()) && empty($model->{$attribute})) {
        $settings['options']['value'] = $behavior->owner->{$behavior->defaultDescriptionAttribute};
    }

    $content .= $form->renderField($model, $attribute, $settings);
}
echo $content;

echo Html::endTag('div');
