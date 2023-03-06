<?php

namespace api\modules\request\controllers;

use api\actions\ActionSubmit;
use api\components\RestController;
use api\modules\request\models\CompetitionRequestForm;

/**
 * Class SubmitController
 * @package api\modules\request\controllers
 */
class SubmitController extends RestController
{
    public $serializer = [
        'class' => \yii\rest\Serializer::class,
        'collectionEnvelope' => 'items'
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['corsFilter']['cors']['Access-Control-Allow-Credentials'] = false;

        return $behaviors;
    }

    /**
     * @return array
     */
    public function actions()
    {
        return [
            'submit-request' => [
                'class' => ActionSubmit::class,
                'modelClass' => CompetitionRequestForm::class,
                'callback' => function (CompetitionRequestForm $model) {
                    return $model->getSuccessMsg();
                }
            ]
        ];
    }
}
