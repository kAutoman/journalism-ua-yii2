<?php

namespace console\migrations;

use common\models\AuthItem;
use common\models\AuthItemChild;
use common\models\User;
use console\components\Migration;
use Yii;
use yii\base\InvalidConfigException;
use yii\rbac\DbManager;

class m181210_124214_add_admin_routes extends Migration
{
    /**
     * @return \yii\rbac\ManagerInterface
     * @throws InvalidConfigException
     */
    protected function getAuthManager()
    {
        $authManager = Yii::$app->getAuthManager();
        if (!$authManager instanceof DbManager) {
            throw new InvalidConfigException('You should configure "authManager" component to use database before executing this migration.');
        }

        return $authManager;
    }

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $authManager = $this->getAuthManager();
        $this->db = $authManager->db;

        $authItem = AuthItem::findOne(['name'=>'/*']);
        if(is_null($authItem)){
            $authItem = new AuthItem();
            $authItem->name = '/*';
            $authItem->type = \yii\rbac\Item::TYPE_PERMISSION;
            $authItem->save();
        }
        $parent = AuthItem::findOne(['name'=>User::ROLE_ADMIN]);
        $check = AuthItemChild::findOne(['parent'=>User::ROLE_ADMIN, 'child' => '/*']);

        if(!is_null($parent) && is_null($check)){
            $authManager->addChild($parent, $authItem);
        }


    }

}
