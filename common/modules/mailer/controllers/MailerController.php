<?php

namespace common\modules\mailer\controllers;

use backend\actions\ActionCreate;
use backend\actions\ActionIndex;
use backend\actions\ActionUpdate;
use backend\actions\ActionView;
use backend\components\BackendController;
use common\modules\mailer\components\Mailer;
use common\modules\mailer\connections\SMTPConnection;
use common\modules\mailer\messages\DbMessage;
use common\modules\mailer\models\MailerLetter;
use common\modules\mailer\models\MailerSetting;
use yii\helpers\ArrayHelper;

/**
 * Class MailerController
 *
 * @package common\modules\mailer\models
 */
class MailerController extends BackendController
{
    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return MailerSetting::class;
    }

    /**
     * @return array
     */
    public function actions()
    {
        return ArrayHelper::merge(parent::actions(), [
            'create' => [
                'class' => ActionCreate::class,
                'modelClass' => $this->getModelClass(),
                'enableAjaxValidation' => true,
            ],
            'update' => [
                'class' => ActionUpdate::class,
                'modelClass' => $this->getModelClass(),
                'enableAjaxValidation' => true,
            ],
            'letters' => [
                'class' => ActionIndex::class,
                'modelClass' => MailerLetter::class
            ],
            'view-letter' => [
                'class' => ActionView::class,
                'modelClass' => MailerLetter::class,
                'view' => 'view-letter'
            ]
        ]);
    }

    /**
     * @return \yii\web\Response
     */
    public function actionDefault()
    {
        $model = MailerSetting::findDefault();

        return $this->asJson($model);
    }

    /**
     * @param int $id MailerSetting Entity
     * @return \yii\web\Response
     */
    public function actionTestConnection(int $id)
    {
        $model = MailerSetting::findOneOrFail(['id' => $id]);
        $connection = new SMTPConnection($model);
        $message = new DbMessage($model);

        $mailer = new Mailer();
        $mailer->setConnection($connection)->createMessage($message)->send(true);
        session()->setFlash('success', [bt('Email sent', 'mailer')], false);

        return $this->redirect('index');
    }

    /**
     * @param int $id
     * @return \yii\web\Response
     */
    public function actionResend(int $id)
    {
        /** @var MailerLetter $letter */
        $letter = MailerLetter::findOneOrFail(['id' => $id]);
        $model = $letter->connection;

        $connection = new SMTPConnection($model);
        $message = new DbMessage($model);
        $mailer = new Mailer();
        $mailer->setConnection($connection)->createMessage($message)->send(true);

        session()->setFlash('success', [bt('Email sent', 'mailer')], false);

        return $this->redirect(['letters']);
    }

    /**
     * @param $action
     * @return bool
     */
    public function beforeAction($action)
    {
        if ($action->id === 'letters') {
            $this->canCreate = false;
        }
        return parent::beforeAction($action);
    }
}
