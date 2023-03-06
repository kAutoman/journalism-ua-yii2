<?php
/**
 * Author: metal
 * Email: metal
 */

namespace backend\modules\menu\widgets;

use Yii;

/**
 * Class LoginMenu
 * @package backend\modules\menu\widgets
 */
class LoginMenu extends BaseMenu
{
    /**
     * @inheritdoc
     */
    public $options = ['class' => 'navbar-nav navbar-right'];


    /**
     * @inheritdoc
     */
    public function init()
    {
        if (Yii::$app->user->isGuest) {
            $this->items = [
                ['label' => 'Login', 'url' => ['/admin/default/login']],
            ];
        }
        parent::init();
    }
}
