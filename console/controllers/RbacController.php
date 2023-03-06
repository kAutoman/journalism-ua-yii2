<?php

namespace console\controllers;

use common\components\UserIdentity;
use common\models\User;
use Yii;
use yii\console\Controller;
use yii\rbac\DbManager;
use yii\rbac\ManagerInterface;
use yii\rbac\Role;
use yii\rbac\Rule;

/**
 */
class RbacController extends Controller
{
    public $adminEmail = 'admin@dev.dev';

    /** @var ManagerInterface */
    private $auth;

    public function __construct($id, $module, DbManager $auth, array $config = [])
    {
        $this->auth = $auth;

        parent::__construct($id, $module, $config);
    }


    public function actionInit()
    {
        $this->addRole(User::ROLE_ADMIN, 'Admin role');
    }

    public function actionRemoveAll()
    {
        if (!$this->confirm("Are you sure? It will delete permissions tree.")) {
            return self::EXIT_CODE_NORMAL;
        }

        $this->auth->removeAll();

        return self::EXIT_CODE_NORMAL;
    }

    /**
     * @param $admin
     *
     * @return bool|null|\yii\rbac\Assignment
     */
    public function assignAdmin(Role $admin)
    {
        $user = UserIdentity::findByEmail($this->adminEmail);
        if (!$user) {
            return false;
        }
        $userId = $user->getId();

        return $this->assignRole($admin, $userId);
    }

    public function assignUsers(Role $user)
    {
        $models = User::find()->select(['id'])->asArray()->all();
        foreach ($models as $one) {
            $this->assignRole($user, $one['id']);
        }
    }

    /**
     * @param $roleName
     * @param $description
     * @return mixed
     */
    public function addRole($roleName, $description)
    {
        $role = $this->auth->getRole($roleName);

        if (!$role) {
            $role = $this->auth->createRole($roleName);
            $this->auth->add($role);
        }

        $role->description = $description;

        $this->auth->update($role->name, $role);

        $this->assignAdmin($role);

        return $role;
    }

    /**
     * @param Rule $item
     * @param Role $parent
     */
    public function permission(Rule $item, Role $parent)
    {
        $rule = $this->auth->getRule($item->name);

        if (!$rule) {
            $this->auth->add($item);
        }

        $this->auth->update($item->name, $item);

        $permission = $this->auth->getPermission($item::$permission);
        if (!$permission) {
            $permission = $this->auth->createPermission($item::$permission);
            $this->auth->add($permission);
        }

        $permission->description = $item::$description;
        $permission->ruleName = $item->name;
        $permission->data = null;

        $this->auth->update($permission->name, $permission);

        $this->addChild($parent, $permission);
    }

    /**
     * @param Role $role
     * @param $userId
     *
     * @return mixed
     */
    public function assignRole(Role $role, $userId)
    {
        $assignment = $this->auth->getAssignment($role->name, $userId);

        if (!$assignment) {
            return $this->auth->assign($role, $userId);
        }

        return $assignment;
    }

    /**
     * @param Role $parent
     * @param $child
     */
    public function addChild(Role $parent, $child)
    {
        $children = $this->auth->getChildren($parent->name);

        if (!in_array($child->name, array_keys($children), true)) {
            $this->auth->addChild($parent, $child);
        }
    }
}
