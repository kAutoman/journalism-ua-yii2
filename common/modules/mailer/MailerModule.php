<?php

namespace common\modules\mailer;

use common\modules\mailer\assets\MailerAssets;
use common\modules\mailer\models\MailerLetter;
use yii\base\Module;

/**
 * Class MailerModule
 *
 * @package common\modules\mailer
 */
class MailerModule extends Module
{
    /**
     * @var string
     */
    public $controllerNamespace = 'common\modules\mailer\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        MailerAssets::register(view());

        parent::init();
    }
}
