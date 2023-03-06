<?php

namespace common\modules\mailer\models;

use Yii;
use common\components\model\ActiveRecord;
use backend\components\BackendModel;
use backend\components\grid\StylingActionColumn;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\jui\DatePicker;

/**
 * This is the model class for table "{{%mailer_letter}}".
 *
 * @property integer $id
 * @property string $connection_id
 * @property string $date_create
 * @property string $date_update
 * @property string $status
 * @property string $subject
 * @property string $body
 * @property string $recipients
 * @property string $attachments
 * @property MailerSetting $connection
 */
class MailerLetter extends MailerModel implements BackendModel
{
    public static function tableName()
    {
        return '{{%mailer_letter}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date_create', 'subject', 'recipients', 'status'], 'required'],
            [['date_create', 'date_update', 'connection_id', 'status'], 'integer'],
            [['body', 'recipients', 'attachments'], 'string'],
            [['subject'], 'string', 'max' => 255],
            [['date_update'], 'default', 'value' => null],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('back/mailer', 'ID'),
            'connection_id' => Yii::t('back/mailer', 'Connection'),
            'date_create' => Yii::t('back/mailer', 'Date create'),
            'date_update' => Yii::t('back/mailer', 'Date update'),
            'subject' => Yii::t('back/mailer', 'Subject'),
            'body' => Yii::t('back/mailer', 'Letter body'),
            'recipients' => Yii::t('back/mailer', 'Recipients'),
            'attachments' => Yii::t('back/mailer', 'Attachments'),
        ];
    }

    /**
    * Get title for the template page
    *
    * @return string
    */
    public function getTitle()
    {
        return Yii::t('back/mailer', 'Mailer Letter');
    }

    /**
     * @return string
     */
    public function getStatusAttribute(): string
    {
        return 'status';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConnection()
    {
        return $this->hasOne(MailerSetting::class, ['id' => 'connection_id']);
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
                    [
                        'attribute' => 'date_create',
                        'format' => 'datetime',
                        'filter' => DatePicker::widget([
                            'model' => $this,
                            'attribute' => 'date_create',
                            'options' => ['class' => 'form-control']
                        ])
                    ],
                    [
                        'attribute' => 'date_update',
                        'format' => 'datetime',
                        'filter' => DatePicker::widget([
                            'model' => $this,
                            'attribute' => 'date_update',
                            'options' => ['class' => 'form-control']
                        ])
                    ],
//                    'connection.label',
                    'subject',
//                    'body',
                    [
                        'attribute' => 'recipients',
                        'format' => 'html',
                        'value' => function (self $model) {
                            return $model->getRecipientsList();
                        }
                    ],
//                    'attachments',
                    [
                        'attribute' => 'status',
                        'filter' => $this->getStatusList(),
                        'value' => function (self $model) {
                            return $model->getStatusLabel($model->status);
                        }
                    ],
                    [
                        'class' => StylingActionColumn::class,
                        'template' => '{view}',
                        'urlCreator' => function ($action, $model, $key, $index) {
                            if ($action === 'view') {
                                $url = Url::to(['view-letter', 'id' => $model->id]);
                                return $url;
                            }
                            return '';
                        }
                    ],
                ];
            break;
            case 'view':
                return [
//                    'id',
                    [
                        'attribute' => 'status',
                        'value' => $this->getStatusLabel($this->status)
                    ],
                    'subject',
                    'body:html',
                    'date_create:datetime',
                    'date_update:datetime',
                    [
                        'attribute' => 'recipients',
                        'format' => 'html',
                        'value' => $this->getRecipientsList()
                    ],
                    'attachments',
                ];
            break;
        }

        return [];
    }

    /**
     * @return string
     */
    public function getRecipientsList(): string
    {
        $recipients = Json::decode($this->recipients);

        $out = '';
        foreach ($recipients as $key => $recipient) {
            $out .= Html::tag('b', bt($key, 'mailer')) . ':<br>';
            $out .= is_array($recipient) ? implode("<br>", $recipient) : $recipient;
            $out .= "<br>";
        }

        return $out;
    }

    /**
    * @return MailerLetterSearch
    */
    public function getSearchModel()
    {
        return new MailerLetterSearch();
    }

    /**
    * @return array
    */
    public function getFormConfig()
    {
        return [];
    }
}
