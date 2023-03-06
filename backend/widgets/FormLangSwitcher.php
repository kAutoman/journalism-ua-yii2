<?php

namespace backend\widgets;

use common\helpers\LanguageHelper;
use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Class FormLangSwitcher
 *
 * @package backend\widgets
 */
class FormLangSwitcher extends Widget
{
    /**
     * @var array
     */
    public $options = [
        'class' => 'edit-langs pull-right',
    ];

    /**
     * @var string
     */
    private $alertMsg = 'All data will be lost. Are you sure?';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->options = ArrayHelper::merge($this->options, ['data' => ['msg' => $this->alertMsg]]);
        return parent::init();
    }

    /**
     * @return string|void
     */
    public function run()
    {
        echo Html::tag('div', $this->getLanguageLinks(), $this->options);
    }

    /**
     * @return string
     */
    public function getLanguageLinks(): string
    {
        $editLocale = request()->get(urlManager()->langParam) ?? LanguageHelper::getDefaultLanguage()->code;

        $links = '';
        foreach (LanguageHelper::getEditableLanguages() as $lang) {
            $activeClass = $editLocale === $lang ? 'active_lang' : '';
            $links .= Html::a(
                Html::tag('span', '', ['class' => "flag-icon flag-icon-{$lang}"]),
                Url::current([urlManager()->langParam => $lang]),
                ['class' => $activeClass]
            );
        }

        return $links;
    }
}
