<?php

namespace backend\components\grid;

use Yii;
use lav45\translate\grid\ActionColumn;
use common\helpers\LanguageHelper;

/**
 * Class TranslateColumn
 *
 * @package backend\components\grid
 */
class TranslateColumn extends ActionColumn
{
    public function init()
    {
        $this->languageAttribute = urlManager()->langParam;
        $this->languages = LanguageHelper::getEditableLanguages();
        $this->header = Yii::t('back/app', 'Translations');
        return parent::init();
    }
}
