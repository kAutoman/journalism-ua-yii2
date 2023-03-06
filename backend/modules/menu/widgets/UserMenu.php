<?php

namespace backend\modules\menu\widgets;

use common\models\User;
use Yii;
use yii\base\Widget;

/**
 * Class RightBarMenu
 *
 * @package backend\modules\menu\widgets
 */
class UserMenu extends Widget
{
    /**
     * @var array
     */
    public $items;

    public function init()
    {
        parent::init();
        /** @var User $user */
        $user = Yii::$app->user->getIdentity();
        $this->items = [
            [
                'label' => $user->getUserName(),
                'icon' => 'user',
                'items' => [
                    [
                        'label' => 'Logout',
                        'url' => ['/logout'],
                        'linkOptions' => ['data-method' => 'post']
                    ]
                ],
            ]
        ];
    }

    public function run()
    {
        if (Yii::$app->getUser()->getIsGuest()) {
            return null;
        }
        return $this->render('userMenuView', [
            'items' => $this->items
        ]);
    }
}
