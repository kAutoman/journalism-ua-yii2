<?php

namespace backend\modules\user\models;

use backend\components\FormBuilder;
use backend\components\grid\StylingActionColumn;
use common\models\AuthItem;
use kartik\grid\SerialColumn;
use Yii;
use common\components\model\ActiveRecord;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;

/**
 * Class Permission
 *
 * @package backend\modules\user\models
 */
class Permission extends AuthItem
{
    /**
     * @return array
     */
    public function rules()
    {
        $rules = [
            [['name'], 'string', 'max' => 50],
            [['name'], 'required'],
            [['description'], 'string', 'max' => 120]
        ];

        return ArrayHelper::merge(parent::rules(), $rules);
    }

    public function getTitle()
    {
        return Yii::t('back/permission', 'Permissions');
    }

    public function getColumns()
    {
        return [
            ['class' => SerialColumn::class],
            'name',
            'description',
            ['class' => StylingActionColumn::class]
        ];
    }

    /**
     * @return ArrayDataProvider
     */
    public static function getDataProvider()
    {
        $dataProvider = new ArrayDataProvider([
            'allModels' => Yii::$app->getAuthManager()->getPermissions()
        ]);

        return $dataProvider;
    }

    public function getPermissions()
    {
        return Yii::$app->getAuthManager()->getPermissions();
    }

    /**
     * @param string $name
     * @return bool
     */
    public function createPermission(string $name): bool
    {
        $permission = Yii::$app->authManager->createPermission($name);
        $permission->description = Yii::$app->request->post('description', '');

        return Yii::$app->authManager->add($permission);
    }

    public function getFormConfig()
    {
        return [
            Yii::t('back/app', 'Main') => [
                'name' => ['type' => FormBuilder::INPUT_TEXT],
                'description' => ['type' => FormBuilder::INPUT_TEXT],
            ]
        ];
    }
}
