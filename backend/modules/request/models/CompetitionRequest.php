<?php

namespace backend\modules\request\models;

use backend\modules\rbac\models\User;
use common\models\MemberItem;
use common\modules\mailer\components\Mailer;
use common\modules\mailer\connections\SMTPConnection;
use common\modules\mailer\messages\DbMessage;
use common\modules\mailer\models\MailerSetting;
use Yii;
use common\models\CompetitionRequest as CommonCompetitionRequest;
use backend\components\FormBuilder;
use backend\components\BackendModel;
use backend\components\grid\StylingActionColumn;


/**
 * This is the model class for table "{{%competition_request}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $gender
 * @property string $age
 * @property string $city
 * @property string $position
 * @property string $company_name
 * @property string $phone
 * @property string $experience
 * @property string $other_name
 * @property string $material_label
 * @property string $material_type
 * @property string $program_label
 * @property string $program_published_date
 * @property string $program_link
 * @property string $nomination
 * @property string $argument
 * @property string $awards
 * @property integer $created_at
 * @property integer $updated_at
 */
class CompetitionRequest extends CommonCompetitionRequest implements BackendModel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'name',
                    'email',
                    'gender',
                    'age',
                    'city',
                    'position',
                    'company_name',
                    'phone',
                    'experience',
                    'material_label',
                    'material_type',
                    'program_label',
                    'program_published_date',
                    'program_link',
                    'nomination',
                    'nomination_id',
                    'argument',
                    'awards',
                    'status',
                ],
                'required',
            ],
            [
                [
                    'name',
                    'email',
                    'gender',
                    'age',
                    'city',
                    'position',
                    'company_name',
                    'phone',
                    'experience',
                    'other_name',
                    'material_type',
                    'program_label',
                    'program_published_date',
                ],
                'string',
                'max' => 255
            ],
            [
                [
                    'material_label',
                    'program_link',
                    'nomination',
                    'argument',
                    'awards',
                    'moderator_comment',
                    'email_message',
                ],
                'string'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('back/request', 'ID'),
            'name' => Yii::t('back/request', 'Name'),
            'email' => Yii::t('back/request', 'Email'),
            'gender' => Yii::t('back/request', 'Gender'),
            'age' => Yii::t('back/request', 'Age'),
            'city' => Yii::t('back/request', 'City'),
            'position' => Yii::t('back/request', 'Position'),
            'company_name' => Yii::t('back/request', 'Company name'),
            'phone' => Yii::t('back/request', 'Phone'),
            'experience' => Yii::t('back/request', 'Experience'),
            'other_name' => Yii::t('back/request', 'Other Name'),
            'material_label' => Yii::t('back/request', 'Material Label'),
            'material_type' => Yii::t('back/request', 'Material Type'),
            'program_label' => Yii::t('back/request', 'Program Label'),
            'program_published_date' => Yii::t('back/request', 'Program Published Date'),
            'program_link' => Yii::t('back/request', 'Program Link'),
            'nomination' => Yii::t('back/request', 'Nomination'),
            'nomination_id' => Yii::t('back/request', 'Nomination'),
            'argument' => Yii::t('back/request', 'Argument'),
            'awards' => Yii::t('back/request', 'Awards'),
            'status' => Yii::t('back/request', 'Status'),
            'moderator_comment' => Yii::t('back/request', 'Moderator Comment'),
            'email_message' => Yii::t('back/request', 'Email Message'),
            'created_at' => Yii::t('back/request', 'Created At'),
            'updated_at' => Yii::t('back/request', 'Updated At'),
        ];
    }

    /**
     * Get title for the template page
     *
     * @return string
     */
    public function getTitle()
    {
        return Yii::t('back/request', 'Competition Request');
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
                    'created_at:datetime',
                    'name',
                    'email',
                    'company_name',
                    'phone',
                    [
                        'attribute' => 'nomination_id',
                        'value' => function (self $model) {
                            return $model->nominationItem->label ?? null;
                        },
                        'filter' => \backend\modules\member\models\MemberItem::getListByUser(),
                    ],
//                    [
//                        'attribute' => 'status',
//                        'value' => function (self $model) {
//                            return $model->getStatus();
//                        },
//                        'filter' => self::getStatuses(),
//                    ],
                    [
                        'class' => StylingActionColumn::class,
                        'template' => '{update} {delete}',
                        'visibleButtons' => [
                            'delete' => user()->can(User::ROLE_JURY_ADMIN),
                        ]
                    ],
                ];
                break;
            case 'view':
                return [
                    'id',
                    'name',
                    'email',
                    'gender',
                    'age',
                    'city',
                    'position',
                    'company_name',
                    'phone',
                    'experience',
                    'other_name',
                    'material_label',
                    'material_type',
                    'program_label',
                    'program_published_date',
                    'program_link',
                    'nomination',
                    'argument',
                    'awards',
                ];
                break;
        }

        return [];
    }

    /**
     * @return CompetitionRequestSearch
     */
    public function getSearchModel()
    {
        return new CompetitionRequestSearch();
    }

    /**
     * @return array
     */
    public function getFormConfig()
    {
        return [
            Yii::t('back/app', 'Main') => [
                'name' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'email' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'gender' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'age' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'city' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'company_name' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'position' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'phone' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'experience' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'other_name' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'material_label' => [
                    'type' => FormBuilder::INPUT_TEXTAREA,
                ],
                'material_type' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'program_label' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'program_published_date' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'program_link' => [
                    'type' => FormBuilder::INPUT_TEXTAREA,
                ],
                'nomination' => [
                    'type' => FormBuilder::INPUT_TEXTAREA,
                ],
                'nomination_id' => [
                    'type' => FormBuilder::INPUT_DROPDOWN_LIST,
                    'items' => MemberItem::getListItems(),
                    'options' => ['prompt' => '']
                ],
                'argument' => [
                    'type' => FormBuilder::INPUT_TEXTAREA,
                ],
                'awards' => [
                    'type' => FormBuilder::INPUT_TEXTAREA,
                ],
                'status' => [
                    'type' => FormBuilder::INPUT_DROPDOWN_LIST,
                    'items' => self::getStatuses(),
                ],
                'moderator_comment' => [
                    'type' => FormBuilder::INPUT_TEXTAREA,
                ],
                'email_message' => [
                    'type' => FormBuilder::INPUT_TEXTAREA,
                ],
            ],
        ];
    }

    /**
     * @param bool $insert
     *
     * @return bool
     * @throws \Throwable
     * @throws \common\modules\mailer\exceptions\InvalidMailerCredentialsException
     * @throws \yii\db\StaleObjectException
     */
    public function beforeSave($insert)
    {
        $oldStatus = (int)$this->getOldAttribute('status');
        $status = (int)$this->getAttribute('status');

        if ($oldStatus != $status) {
            if ($status == self::STATUS_REJECT) {
                $this->sendUserEmail();
            }
        }

        return parent::beforeSave($insert);
    }

    /**
     * @throws \Throwable
     * @throws \common\modules\mailer\exceptions\InvalidMailerCredentialsException
     * @throws \yii\db\StaleObjectException
     */
    protected function sendUserEmail()
    {
        $mail = MailerSetting::findOne(1);

        $mail->send_to = $this->email;

        $mailer = new Mailer();
        $message = new DbMessage($mail);
        $connection = new SMTPConnection($mail);

        $mailer->setConnection($connection)
            ->createMessage($message, ['message' => $this->email_message])
            ->send();
    }
}
