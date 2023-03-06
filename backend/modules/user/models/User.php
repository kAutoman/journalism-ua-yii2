<?php

namespace backend\modules\user\models;

use common\models\AuthAssignment;
use yii\behaviors\TimestampBehavior;
use common\models\User as CommonUser;
use backend\components\BackendModel;
use backend\components\FormBuilder;
use backend\components\grid\StylingActionColumn;

/**
 * Class User
 *
 * @property string $password
 * @property string $password_confirm
 *
 * @property AuthAssignment $authAdmin
 * @property string $title
 * @property \yii\db\ActiveRecord $searchModel
 * @property mixed $admin
 * @property array $changePasswordForm
 * @property array $formConfig
 * @property AuthAssignment $authModerator
 */
class User extends CommonUser implements BackendModel
{
    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $password_confirm;

    /**
     * @var string
     */
    public $sign;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
            ],
        ];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        return [
            'change-password' => ['password', 'password_confirm'],
            'create' => ['username', 'email', 'password', 'password_confirm'],
            'update' => ['username', 'email', 'password', 'password_confirm'],
        ];
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['status'], 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],

            [['username'], 'filter', 'filter' => 'trim'],
            [['username'], 'required'],
            [['username'], 'string', 'min' => 2, 'max' => 255],

            [['email'], 'filter', 'filter' => 'trim'],
            [['email'], 'required'],
            [['email'], 'email'],

            [
                ['email'],
                'unique',
                'targetClass' => User::class,
                'message' => 'This email address has already been taken.'
            ],
            [['auth_key', 'password_hash', 'password_reset_token'], 'safe'],

            [['username', 'email', 'password', 'password_confirm'], 'required', 'on' => 'create'],

            [['password', 'password_confirm'], 'required', 'on' => 'change-password'],
            ['password_confirm', 'compare', 'compareAttribute' => 'password', 'on' => ['change-password', 'create']],

            [['sign'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => bt('User name', 'user'),
            'auth_key' => bt('Auth Key', 'user'),
            'password_hash' => bt('Password Hash', 'user'),
            'password_reset_token' => bt('Password Reset Token', 'user'),
            'email' => bt('Email', 'user'),
            'status' => bt('Status', 'user'),
            'block_at' => bt('Block At', 'user'),
            'password' => bt('Password', 'user'),
            'password_confirm' => bt('Confirm password', 'user'),
        ];
    }

    /**
     * Get title for the template page
     *
     * @return string
     */
    public function getTitle()
    {
        return bt('User', 'user');
    }

    /**
     * Get attribute columns for index and view page
     *
     * @param $page
     *
     * @return array
     */
    public function getColumns($page)
    {
        switch ($page) {
            case 'index':
                return [
                    [
                        'attribute' => 'id',
                        'headerOptions' => [
                            'width' => '30px',
                        ],
                    ],
                    'username',
                    'email:email',
                    [
                        'label' => bt('Change password', 'user'),
                        'format' => 'raw',
                        'value' => function (self $model) {
                            return a(
                                bt('Change password', 'user'),
                                ['/user/user/change-password', 'id' => $model->id]
                            );
                        }
                    ],
                    [
                        'class' => StylingActionColumn::class,
                        'template' => '{update} {delete}',
                        'visibleButtons' => [
                            'delete' => function ($model) {
                                return $model->id !== app()->getUser()->id;
                            }
                        ]
                    ]
                ];
                break;
            case 'view':
                return [
                    'id',
                    'username',
                    'email:email',
                ];
                break;
        }
        return [];
    }

    /**
     * @return \yii\db\ActiveRecord
     */
    public function getSearchModel()
    {
        return new UserSearch();
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getFormUpdate()
    {
        return [
            bt('Main', 'user') => [
                'username' => [
                    'type' => FormBuilder::INPUT_TEXT,
                    'options' => ['maxlength' => true],
                ],
                'email' => [
                    'type' => FormBuilder::INPUT_TEXT,
                    'options' => ['maxlength' => true],
                ],
            ]
        ];
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getFormConfig()
    {
        return [
            bt('Main', 'user') => [
                'username' => [
                    'type' => FormBuilder::INPUT_TEXT,
                    'options' => ['maxlength' => true],
                ],
                'email' => [
                    'type' => FormBuilder::INPUT_TEXT,
                    'options' => ['maxlength' => true],
                ],
                'password' => [
                    'type' => FormBuilder::INPUT_PASSWORD,
                    'options' => ['maxlength' => true]
                ],
                'password_confirm' => [
                    'type' => FormBuilder::INPUT_PASSWORD,
                    'options' => ['maxlength' => true]
                ],
            ]
        ];
    }

    /**
     * @return array
     */
    public function getChangePasswordForm()
    {
        return [
            bt('Main', 'user') => [
                'password' => [
                    'type' => FormBuilder::INPUT_PASSWORD,
                    'options' => [
                        'maxlength' => true,
                        'autocomplete' => 'off',
                    ],
                ],
                'password_confirm' => [
                    'type' => FormBuilder::INPUT_PASSWORD,
                    'options' => [
                        'maxlength' => true,
                        'autocomplete' => 'off',
                    ],
                ],
            ]
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthAdmin()
    {
        return $this->hasOne(AuthAssignment::class, ['user_id' => 'id'])->andWhere(['item_name' => User::ROLE_ADMIN]);
    }

    /**
     * @return mixed
     */
    public function getAdmin()
    {
        return !!$this->authAdmin;
    }

    /**
     * @param bool $insert
     *
     * @return bool
     * @throws \yii\base\Exception
     */
    public function beforeSave($insert)
    {
        if ($this->password) {
            $this->auth_key = security()->generateRandomString();
            $this->password_hash = security()->generatePasswordHash($this->password);
            $this->password_reset_token = security()->generateRandomString() . '_' . time();
        }

        return parent::beforeSave($insert);
    }

    /**
     * Adding default role for new users.
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($this->id) {
            $exist = AuthAssignment::find()->where(['user_id' => $this->id])->exists();
            if (!$exist) {
                $model = new AuthAssignment([
                    'user_id' => (string) $this->id,
                    'item_name' => self::ROLE_ADMIN,
                    'created_at' => time()
                ]);
                $model->save();
            }
        }
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * Delete role.
     * @return bool
     */
    public function beforeDelete()
    {
        AuthAssignment::deleteAll(['user_id' => $this->id]);
        return parent::beforeDelete();
    }
}
