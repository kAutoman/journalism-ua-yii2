<?php

namespace common\components;

use vintage\i18n\components\I18N as baseI18N;

class I18N extends baseI18N
{
    public $missingTranslationHandler = ['common\components\I18NModule', 'missingTranslation'];
}
