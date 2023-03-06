<?php

namespace common\behaviors;

use common\helpers\LanguageHelper;
use Yii;
use lav45\translate\TranslatedBehavior as BaseTranslatedBehavior;

/**
 * Class TranslatedBehavior
 *
 * @package common\modules\builder\behaviors
 */
class TranslatedBehavior extends BaseTranslatedBehavior
{
    public $owner;

    /**
     * @var string the translations relation name
     */
    public $translateRelation = 'translations';

    /**
     * @var string the translations model language attribute name
     */
    public $languageAttribute = 'language';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->setLanguage(request()->get(urlManager()->langParam, app()->language));
    }
}
