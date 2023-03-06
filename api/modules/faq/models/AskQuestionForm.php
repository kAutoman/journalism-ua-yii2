<?php

namespace api\modules\faq\models;

use common\modules\faq\models\FaqAskQuestion;
use common\modules\config\application\components\AggregateMaker;
use yii\behaviors\TimestampBehavior;
use common\modules\config\application\entities\AskQuestionForm as AskQuestionFormConfig;

/**
 * Class AskQuestionForm
 *
 * @package api\modules\faq\models
 */
class AskQuestionForm extends FaqAskQuestion
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['name', 'email', 'question'], 'required'],
            [['name', 'email', 'phone'], 'string', 'max' => 100],
            [['question'], 'string', 'max' => 1000],
            [['email'], 'email'],
        ];
    }

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
    public function getSuccessMsg(): array
    {
        $aggregate = new AggregateMaker(AskQuestionFormConfig::class);
        /** @var AskQuestionFormConfig $formConfig */
        $formConfig = $aggregate->make();

        return [
            'title' => $formConfig->successTitle,
            'description' => $formConfig->successDescription,
        ];
    }

    /**
     * @return string
     */
    public static function getSubmitUrl(): string
    {
        return '/question-request'; // @todo change to `toRoute()`
    }
}
