<?php

namespace common\modules\mailer\models;

use backend\modules\form\models\BaseForm;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Html;
use yii\web\JsExpression;
use common\helpers\UrlHelper;
use common\components\model\ActiveRecord;
use backend\widgets\Editor;
use backend\components\FormBuilder;
use backend\components\BackendModel;
use backend\components\grid\StylingActionColumn;

/**
 * This is the model class for table "{{%mailer_setting}}".
 *
 * @property integer $id
 * @property string $label
 * @property string $subject
 * @property string $template
 * @property string $use_default
 * @property string $smtp_host
 * @property integer $smtp_port
 * @property string $smtp_encryption
 * @property integer $auth
 * @property string $smtp_username
 * @property string $smtp_password
 * @property string $send_from
 * @property string $send_to
 * @property string $send_to_cc
 * @property string $send_to_bcc
 * @property integer $is_default
 * @property integer $created_at
 * @property integer $updated_at
 */
class MailerSetting extends ActiveRecord implements BackendModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%mailer_setting}}';
    }

    public function behaviors()
    {
        return [
            'timestamp' => TimestampBehavior::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['label', 'subject', 'smtp_host', 'smtp_port', 'smtp_encryption', 'send_from', 'send_to'], 'required'],
            [['template', 'send_to_cc', 'send_to_bcc'], 'string'],
            [['smtp_port'], 'integer'],
            [['send_from', 'send_to'], 'email'],
            [['send_to_cc', 'send_to_bcc'], 'emails'],
            [['auth', 'is_default', 'use_default'], 'boolean'],
            [
                [
                    'label',
                    'subject',
                    'smtp_host',
                    'smtp_encryption',
                    'smtp_username',
                    'smtp_password',
                    'send_from',
                    'send_to'
                ],
                'string',
                'max' => 255
            ],
            [['auth'], 'default', 'value' => 1],
            [['is_default', 'use_default'], 'default', 'value' => 0],
            [
                ['smtp_username', 'smtp_password'],
                'required',
                'when' => function () {
                    return (bool)$this->auth;
                },
                'whenClient' => new JsExpression("
                function (attribute, value) {
                    return $('#mailersetting-auth').is(':checked');
                }
            ")
            ],
            [['useDefault'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('back/mailer', 'ID'),
            'label' => Yii::t('back/mailer', 'Label'),
            'subject' => Yii::t('back/mailer', 'Subject'),
            'template' => Yii::t('back/mailer', 'Template'),
            'use_default' => Yii::t('back/mailer', 'Use default settings'),
            'smtp_host' => Yii::t('back/mailer', 'SMTP host'),
            'smtp_port' => Yii::t('back/mailer', 'SMTP port'),
            'smtp_encryption' => Yii::t('back/mailer', 'SMTP encryption'),
            'auth' => Yii::t('back/mailer', 'Auth'),
            'smtp_username' => Yii::t('back/mailer', 'SMTP username'),
            'smtp_password' => Yii::t('back/mailer', 'SMTP password'),
            'send_from' => Yii::t('back/mailer', 'Send from'),
            'send_to' => Yii::t('back/mailer', 'Send to'),
            'send_to_cc' => Yii::t('back/mailer', 'Send to cc'),
            'send_to_bcc' => Yii::t('back/mailer', 'Send to bcc'),
            'is_default' => Yii::t('back/mailer', 'Is default'),
            'created_at' => Yii::t('back/mailer', 'Created At'),
            'updated_at' => Yii::t('back/mailer', 'Updated At'),
        ];
    }

    /**
     * Get title for the template page
     *
     * @return string
     */
    public function getTitle()
    {
        return Yii::t('back/mailer', 'Mailer');
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
                    ['class' => 'yii\grid\SerialColumn'],
                    'label',
                    'subject',
                    'send_from',
                    'send_to',
                    [
                        'format' => 'raw',
                        'value' => function (self $model) {
                            return Html::a(Yii::t('back/mailer', 'Test connection'),
                                ['test-connection', 'id' => $model->id]);
                        }
                    ],
                    [
                        'class' => StylingActionColumn::class,
                        'visibleButtons' => [
                            'delete' => function (self $model) {
                                return !$model->is_default;
                            }
                        ]
                    ],
                ];
                break;
            case 'view':
                return [
                    'id',
                    'label',
                    'subject',
                    'template',
                    'smtp_host',
                    'smtp_port',
                    'smtp_encryption',
                    'auth',
                    'smtp_username',
//                    'smtp_password',
                    'send_from',
                    'send_to',
                    'send_to_cc',
                    'send_to_bcc',
                    'is_default',
                ];
                break;
        }

        return [];
    }

    /**
     * @return MailerSettingSearch
     */
    public function getSearchModel()
    {
        return new MailerSettingSearch();
    }

    /**
     * @return array
     */
    public function getFormConfig()
    {
        $config = [
            Yii::t('back/mailer', 'Main') => [
                'label' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
            ],
            Yii::t('back/mailer', 'Message') => [
                'subject' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],

                'template' => [
                    'type' => FormBuilder::INPUT_WIDGET,
                    'widgetClass' => Editor::class,
                    'hint' => Yii::t('back/mailer',
                        'POST-param name in {{double brackets}} will be changed into their values. E.g, `Name: {{name}}` => `Name: John`'),
                    'options' => [
                        'model' => $this,
                        'attribute' => 'template',
                    ]
                ],
                'send_from' => [
                    'type' => FormBuilder::INPUT_TEXT,
                    'hint' => Yii::t('back/mailer', 'Often, the same as in connection settings'),
                ],
                'send_to' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'send_to_cc' => [
                    'type' => FormBuilder::INPUT_TEXTAREA,
                    'hint' => Yii::t('back/mailer', 'Each address from new line'),
                ],
                'send_to_bcc' => [
                    'type' => FormBuilder::INPUT_TEXTAREA,
                    'hint' => Yii::t('back/mailer', 'Each address from new line'),
                ],
            ],
            Yii::t('back/mailer', 'Connection') => [
                'use_default' => [
                    'type' => FormBuilder::INPUT_CHECKBOX,
                    'options' => [
                        'id' => 'mailer-default',
                        'data' => ['url' => $this->getDefaultSettingsUrl()]
                    ]
                ],
                'smtp_host' => [
                    'type' => FormBuilder::INPUT_TEXT,
                    'hint' => 'smtp.gmail.com',
                    'options' => [
                        'disabled' => $this->disabledInput(),
                    ]
                ],
                'smtp_port' => [
                    'type' => FormBuilder::INPUT_TEXT,
                    'hint' => 465,
                    'options' => [
                        'disabled' => $this->disabledInput(),
                    ]
                ],
                'smtp_encryption' => [
                    'type' => FormBuilder::INPUT_TEXT,
                    'hint' => 'tls/ssl',
                    'options' => [
                        'disabled' => $this->disabledInput(),
                    ]
                ],
                'auth' => [
                    'type' => FormBuilder::INPUT_CHECKBOX,
                    'options' => [
                        'disabled' => $this->disabledInput(),
                    ]
                ],
                'smtp_username' => [
                    'type' => FormBuilder::INPUT_TEXT,
                    'options' => [
                        'disabled' => $this->disabledInput(),
                    ]
                ],
                'smtp_password' => [
                    'type' => FormBuilder::INPUT_PASSWORD,
                    'options' => [
                        'disabled' => $this->disabledInput(),
                    ]
                ],
            ],
        ];
        if ($this->isDefault()) {
            unset($config[Yii::t('back/mailer', 'Connection')]['use_default']);
        }

        return $config;
    }

    /**
     * @return bool
     */
    public function disabledInput(): bool
    {
        return !$this->getIsNewRecord() && $this->use_default;
    }

    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->is_default;
    }

    /**
     * @param array $params
     * @return string
     */
    public function getDefaultSettingsUrl(array $params = []): string
    {
        return UrlHelper::createUrl('/mailer/mailer/default', $params);
    }

    /**
     * @return MailerSetting
     */
    public static function findDefault(): self
    {
        return self::findOne(['is_default' => 1]);
    }

    /**
     * @return bool
     */
    public function beforeValidate()
    {
        if ($this->use_default) {
            $this->setDefaultValues();
        }

        return parent::beforeValidate();
    }

    /**
     * @return MailerSetting
     */
    private function setDefaultValues(): MailerSetting
    {
        $defaultModel = self::findDefault();

        $this->smtp_host = $defaultModel->smtp_host;
        $this->smtp_port = $defaultModel->smtp_port;
        $this->smtp_encryption = $defaultModel->smtp_encryption;
        $this->auth = $defaultModel->auth;
        $this->smtp_username = $defaultModel->smtp_username;
        $this->smtp_password = $defaultModel->smtp_password;

        return $this;
    }
}
