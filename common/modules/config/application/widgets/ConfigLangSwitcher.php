<?php

namespace common\modules\config\application\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Url;
use yii\helpers\Html;
use common\helpers\LanguageHelper;

/**
 * Class ConfigLangSwitcher
 */
class ConfigLangSwitcher extends Widget
{
    public function run()
    {
        $links = '';

        foreach (LanguageHelper::getEditableLanguages() as $lang) {
            $link = Url::current([urlManager()->langParam => $lang]);
            $icon = Html::tag('i', null, ['class' => "flag-icon flag-icon-{$lang}"]);
            $links .= '<li>' . Html::a($icon, $link) . '</li>';
        }

        return $links;
    }
}
