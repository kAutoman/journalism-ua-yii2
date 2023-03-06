<?php

namespace console\migrations;

use common\models\User;
use Yii;
use console\components\Migration;
use yii\base\InvalidConfigException;
use yii\rbac\DbManager;
use yii\rbac\Role;

/**
 * Class m200310_060538_create_jury_roles
 * @package console\migrations
 */
class m200310_060538_create_jury_roles extends Migration
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
     * @return bool|void
     * @throws InvalidConfigException
     * @throws \yii\base\Exception
     */
    public function safeUp()
    {
        $authManager = $this->getAuthManager();

        $permission = $authManager->getPermission('/*');

        /** @var Role $roleAdmin */
        $roleAdmin = $authManager->getRole(User::ROLE_ADMIN);

        /** @var Role $roleJuryAdmin */
        $roleJuryAdmin = $authManager->createRole(User::ROLE_JURY_ADMIN);
        $roleJuryAdmin->description = 'Jury admin role';

        $authManager->add($roleJuryAdmin);
        $authManager->addChild($roleJuryAdmin, $permission);
        $authManager->addChild($roleAdmin, $roleJuryAdmin);

        /** @var Role $roleJury */
        $roleJury = $authManager->createRole(User::ROLE_JURY);
        $roleJury->description = 'Jury role';

        $authManager->add($roleJury);
        $authManager->addChild($roleJury, $permission);
        $authManager->addChild($roleJuryAdmin, $roleJury);

        /** @var Role $roleModerator */
        $roleModerator = $authManager->createRole(User::ROLE_MODERATOR);
        $roleModerator->description = 'Moderator role';

        $authManager->add($roleModerator);
        $authManager->addChild($roleModerator, $permission);
        $authManager->addChild($roleJuryAdmin, $roleModerator);
    }

    /**
     * @return bool|void
     * @throws InvalidConfigException
     */
    public function safeDown()
    {
        $authManager = $this->getAuthManager();

        /** @var Role $roleJuryAdmin */
        $roleJuryAdmin = $authManager->getRole(User::ROLE_JURY_ADMIN);
        $authManager->remove($roleJuryAdmin);

        /** @var Role $roleJury */
        $roleJury = $authManager->getRole(User::ROLE_JURY);
        $authManager->remove($roleJury);

        /** @var Role $roleModerator */
        $roleModerator = $authManager->getRole(User::ROLE_MODERATOR);
        $authManager->remove($roleModerator);
    }
}
