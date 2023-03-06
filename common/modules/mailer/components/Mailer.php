<?php

namespace common\modules\mailer\components;

use Yii;
use Throwable;
use Swift_Mailer;
use Swift_Message;
use Swift_Attachment;
use Swift_SmtpTransport;
use Swift_Plugins_LoggerPlugin;
use Swift_Plugins_Loggers_ArrayLogger;
use yii\helpers\Json;
use yii\db\StaleObjectException;
use common\components\model\ActiveRecord;
use common\modules\mailer\connections\SMTPConnection;
use common\modules\mailer\models\MailerLetter;
use common\modules\mailer\interfaces\IMessage;
use common\modules\mailer\interfaces\IConnection;
use common\modules\mailer\exceptions\InvalidMailerCredentialsException;

/**
 * Class Mailer
 *
 * @package app\components
 * @todo PHP-doc
 */
class Mailer
{
    /**
     * @var Swift_Mailer
     */
    protected $mailer;
    /**
     * @var MailerLetter
     */
    protected $message;
    /**
     * @var IConnection
     */
    protected $connection;
    /**
     * @var MailerLetter
     */
    protected $letter;

    /**
     * @param IConnection $connection
     * @return Mailer
     */
    public function setConnection(IConnection $connection): Mailer
    {
        $transport = new Swift_SmtpTransport(
            $connection->getSmtpHost(),
            $connection->getSmtpPort(),
            $connection->getSmtpEncryption()
        );
        $transport->setStreamOptions([
            'ssl' => [
                'verify_peer' => false,
                'allow_self_signed' => true,
                'verify_peer_name' => false,
            ],
        ]);
        if ($connection->getNeedAuth()) {
            $transport->setUsername($connection->getUsername())
                ->setPassword($connection->getPassword());
        }
        $this->connection = $connection;
        $this->setMailer($transport);

        return $this;
    }

    /**
     * @param IMessage $message
     * @param array $messageParams
     * @param array $subjectParams
     * @return Mailer
     */
    public function createMessage(IMessage $message, array $messageParams = [], array $subjectParams = []): Mailer
    {
        $this->message = $this->storeMessage($message, $messageParams, $subjectParams);

        return $this;
    }

    /**
     * @param bool $immediately
     * @return bool
     * @throws InvalidMailerCredentialsException
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function send(bool $immediately = false): bool
    {
        if ($immediately) {
            $message = $this->createSwiftMessage($this->message);
            $this->sendNow($message);
        }

        return true;
    }

    /**
     * @param int $limit
     * @return int
     */
    public function batchSend(int $limit): int
    {
        /** @var MailerLetter[] $letters */
        $letters = $this->getQueuedLetter($limit);
        $counter = 0;
        foreach ($letters as $letter) {
            try {
                $this->setConnection(new SMTPConnection($letter->connection));
                $message = $this->createSwiftMessage($letter);
                $this->sendNow($message);
            } catch (Throwable $e) {
                $this->letter->updateStatus(MailerLetter::STATUS_FAILED);
            }

            $counter++;
        }

        return $counter;
    }

    /**
     * @param Swift_Message $message
     * @return int
     * @throws Throwable
     * @throws StaleObjectException
     */
    private function sendNow(Swift_Message $message): int
    {
        $mailLogger = new Swift_Plugins_Loggers_ArrayLogger();
        $this->mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($mailLogger));

        $send = $this->mailer->send($message);

        if ($send) {
            $this->letter->date_update = Yii::$app->getFormatter()->asTimestamp('now');
            $this->letter->update(false, ['date_update']);
            $this->letter->updateStatus(MailerLetter::STATUS_SUCCESS);
            // todo log with $mailLogger->dump();
        } else {
            $this->letter->updateStatus(MailerLetter::STATUS_FAILED);
        }

        return $send;
    }

    /**
     * @param int $limit
     * @return array
     */
    protected function getQueuedLetter(int $limit): array
    {
        $letterModels = MailerLetter::find()
            ->andWhere(['date_update' => null])
            ->limit($limit)
            ->orderBy(['date_create' => SORT_DESC])
            ->all();

        return $letterModels;
    }

    /**
     * @param MailerLetter $letter
     * @return Swift_Message
     * @throws InvalidMailerCredentialsException
     */
    protected function createSwiftMessage(MailerLetter $letter): Swift_Message
    {
        $this->letter = $letter;
        $letter->updateStatus(MailerLetter::STATUS_SENDING);
        $message = new Swift_Message();
        $message->setSubject($letter->subject)
            ->setBody($letter->body, 'text/html', 'utf-8');
        $recipients = Json::decode($letter->recipients);
        if (!isset($recipients['from']) || !isset($recipients['to'])) {
            throw new InvalidMailerCredentialsException();
        }
        $message->setFrom($recipients['from']);
        $message->setTo($recipients['to']);
        if (isset($recipients['cc'])) {
            $message->setCc($recipients['cc']);
        }
        if (isset($recipients['bcc'])) {
            $message->setBcc($recipients['bcc']);
        }

        if (!empty($letter->attachments)) {
            $attachments = Json::decode($letter->attachments);
            foreach ($attachments as $attachment) {
                $message->attach(
                    Swift_Attachment::fromPath($attachment['path'])
                        ->setFilename("{$attachment['name']}.{$attachment['ext']}")
                );
            }
        }

        //todo attachments

        return $message;
    }

    /**
     * @param IMessage $message
     * @param array $messageParams
     * @param array $subjectParams
     * @return ActiveRecord
     */
    protected function storeMessage(IMessage $message, array $messageParams = [], array $subjectParams = []): ActiveRecord
    {
        $letterModel = new MailerLetter();
        $letterModel->connection_id = $this->connection->getId();
        $letterModel->date_create = Yii::$app->getFormatter()->asTimestamp('now');

        $addresses['from'] = $message->getSendFrom();
        if (!empty($message->getSendTo())) {
            $addresses['to'] = explode("\n", str_replace("\r", '', trim($message->getSendTo())));
        }
        // rewrite $addresses['to'] with dynamic client email if special parameter exists
        if (isset($messageParams['_sendTo'])) {
            $addresses['to'] = $messageParams['_sendTo'];
            unset($messageParams['_sendTo']);
        }
        if (isset($messageParams['attachments'])) {
            $letterModel->attachments = Json::encode($messageParams['attachments']);
            unset($messageParams['attachments']);
        }

        if (!empty($message->getSendToCc())) {
            $addresses['cc'] = explode("\n", str_replace("\r", '', trim($message->getSendToCc())));
        }
        if (!empty($message->getSendToBcc())) {
            $addresses['bcc'] = explode("\n", str_replace("\r", '', trim($message->getSendToBcc())));
        }
        $letterModel->recipients = Json::encode($addresses);

        $letterModel->subject = $this->fillBody($message->getSubject(), $subjectParams);
        $letterModel->body = $this->fillBody($message->getTemplate(), $messageParams);
        $letterModel->status = MailerLetter::STATUS_IN_QUEUE;

        $letterModel->save();

        return $letterModel;
    }

    /**
     * @param string $template
     * @param array $params
     * @return string
     */
    private function fillBody(string $template, array $params): string
    {
        $pairs = [];
        foreach ($params as $key => $value) {
            $pairs["{{" . $key . "}}"] = $value;
        }

        return strtr($template, $pairs);
    }

    /**
     * @param Swift_SmtpTransport $transport
     * @return Swift_Mailer
     */
    protected function setMailer(Swift_SmtpTransport $transport): Swift_Mailer
    {
        $this->mailer = new Swift_Mailer($transport);

        return $this->mailer;
    }
}
