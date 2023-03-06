<?php

namespace api\modules\faq\controllers;

use api\actions\ActionSubmit;
use api\components\RestController;
use api\modules\faq\models\AskQuestionForm;
use api\modules\faq\models\Faq;

/**
 * Class FaqController
 *
 * @package api\modules\faq\controllers
 */
class FaqController extends RestController
{
    public $serializer = [
        'class' => \yii\rest\Serializer::class,
        'collectionEnvelope' => 'items'
    ];

    /**
     * @return array
     */
    public function actions()
    {
        return [
            'index' => [

                'class' => \yii\rest\IndexAction::class,
                'modelClass' => Faq::class,
                'prepareDataProvider' => function () {
                    $searchModel = new Faq();
                    $params = app()->getRequest()->getQueryParams();
                    return $searchModel->getDataProvider($params);
                },
            ],
            'question-request' => [
                'class' => ActionSubmit::class,
                'modelClass' => AskQuestionForm::class,
                'callback' => function (AskQuestionForm $model) {
                    return $model->getSuccessMsg();
                }
            ]
        ];
    }
}
