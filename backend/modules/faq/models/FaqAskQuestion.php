<?php

namespace backend\modules\faq\models;

use Yii;
use Exception;
use yii\helpers\Html;
use yii\jui\DatePicker;
use common\helpers\StringHelper;
use common\modules\faq\models\FaqAskQuestion as CommonFaqAskQuestion;
use backend\components\FormBuilder;
use backend\components\BackendModel;
use backend\components\grid\StylingActionColumn;

/**
 * This is the model class for table "{{%faq_ask_question}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $question
 * @property integer $created_at
 * @property integer $updated_at
 */
class FaqAskQuestion extends CommonFaqAskQuestion implements BackendModel
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['question'], 'string', 'max' => 500],
            [['name', 'email', 'phone'], 'string', 'max' => 255],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('back/faq-ask-question', 'ID'),
            'name' => Yii::t('back/faq-ask-question', 'Name'),
            'email' => Yii::t('back/faq-ask-question', 'Email'),
            'phone' => Yii::t('back/faq-ask-question', 'Phone'),
            'question' => Yii::t('back/faq-ask-question', 'Question'),
            'created_at' => Yii::t('back/faq-ask-question', 'Created At'),
            'updated_at' => Yii::t('back/faq-ask-question', 'Updated At'),
        ];
    }

    /**
    * Get title for the template page
    *
    * @return string
    */
    public function getTitle()
    {
        return Yii::t('back/faq-ask-question', 'Ask question requests');
    }

    /**
     * Get attribute columns for index and view page
     *
     * @param $page
     * @return array
     * @throws Exception
     */
    public function getColumns($page)
    {
        switch ($page) {
            case 'index':
                return [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'name',
                        'options' => ['width' => '30%'],
                        'format' => 'html',
                        'value' => function (self $model) {
                            $value = [
                                Html::tag('span', "<strong>{$this->getAttributeLabel('name')}</strong>: {$model->name}"),
                                Html::tag('span', "<strong>{$this->getAttributeLabel('email')}</strong>: {$model->email}"),
                                Html::tag('span', "<strong>{$this->getAttributeLabel('phone')}</strong>: {$model->phone}"),
                            ];

                            return implode('<br> ', $value);
                        }
                    ],
                    [
                        'attribute' => 'question',
                        'format' => 'html',
                        'value' => function (self $model) {
                            $text = StringHelper::truncate($model->question, 300);
                            return Html::a($text, ['view', 'id' => $model->id]);
                        }
                    ],
                    [
                        'attribute' => 'created_at',
                        'format' => 'datetime',
                        'filter' => DatePicker::widget([
                            'model' => $this,
                            'attribute' => 'created_at',
                            'options' => ['class' => 'form-control']
                        ]),
                        'options' => ['width' => '15%'],
                    ],
                    [
                        'class' => StylingActionColumn::class,
                        'template' => '{view}'
                    ],
                ];
            break;
            case 'view':
                return [
                    'id',
                    'name',
                    'email',
                    'phone',
                    'question:ntext',
                    'created_at:datetime',
                ];
            break;
        }

        return [];
    }

    /**
    * @return FaqAskQuestionSearch
    */
    public function getSearchModel()
    {
        return new FaqAskQuestionSearch();
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
                'phone' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'question' => [
                    'type' => FormBuilder::INPUT_TEXTAREA
                ],
            ],
        ];
    }

    /**
     * @return int
     */
    public static function getRequestsCount(): int
    {
        return self::find()->count();
    }
}
