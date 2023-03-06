<?php
/**
 * Author: Pavel Naumenko
 */

namespace backend\modules\menu\widgets;

use common\models\User;
use Yii;

/**
 * Class RightBarMenu
 * @package backend\modules\menu\widgets
 */
class RightBarMenu extends BaseMenu
{
    /**
     * @inheritdoc
     */
    public $options = ['class' => 'navbar-nav navbar-right'];
    /**
     * @inheritdoc
     */
    public $dropdownIndicator = '<span class="caret-dot"></span>';
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
            $userName = Yii::$app->user->identity->username;
            $this->items = [
                [
                    'label' => $userName,
                    'items' => require(Yii::getAlias('@backend/config/right-bar-menu-items.php'))
                ]
            ];
        }
        parent::init();
    }
}
