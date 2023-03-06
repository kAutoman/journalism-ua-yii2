<?php
/**
 * Author: metal
 * Email: metal
 */

namespace backend\modules\menu\widgets;

use common\models\User;
use Yii;

/**
 * Class MainMenu
 * @package backend\modules\menu\widgets
 */
class MainMenu extends BaseMenu
{
    /**
     * @inheritdoc
     */
    public $options = ['class' => 'navbar-nav navbar-left no-active-background'];
    /**
     * @inheritdoc
     */
    public $activateParents = true;


    /**
     * @inheritdoc
     */
    public function init()
    {
        if (Yii::$app->user->can(User::ROLE_ADMIN)) {
            $this->items = require(Yii::getAlias('@backend/config/menu-items.php'));
        }

        parent::init();
    }
}
