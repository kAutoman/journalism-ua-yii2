<?php

namespace common\modules\mailer\messages;

use common\modules\mailer\connections\SMTPConnection;
use common\modules\mailer\interfaces\IConnection;
use common\modules\mailer\interfaces\IMessage;
use common\modules\mailer\models\MailerSetting;

/**
 * Class DbMessage
 *
 * @package common\modules\mailer\messages
 */
class DbMessage implements IMessage
{
    protected $model;

    public function __construct(?MailerSetting $model = null)
    {
        $this->model = $model ?? MailerSetting::findDefault();
    }

    public function getSubject(): string
    {
        return $this->model->subject;
    }

    public function getTemplate(): string
    {
        return $this->model->template;
    }

    public function getSendFrom(): string
    {
        return $this->model->send_from;
    }

    public function getSendTo(): string
    {
        return $this->model->send_to;
    }

    public function getSendToCc(): ?string
    {
        return $this->model->send_to_cc;
    }

    public function getSendToBcc(): ?string
    {
        return $this->model->send_to_bcc;
    }

    public function getAttachments(): ?string
    {
        return null; //todo attachments
    }
}
