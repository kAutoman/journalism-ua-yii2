<?php

namespace common\modules\mailer\connections;

use common\modules\mailer\interfaces\IConnection;
use common\modules\mailer\models\MailerSetting;

/**
 * Class SMTPConnection
 *
 * @package common\modules\mailer\connections
 */
class SMTPConnection implements IConnection
{
    protected $model;

    public function __construct(?MailerSetting $model = null)
    {
        $this->model = $model ?? MailerSetting::findDefault();
    }

    public function getId(): int
    {
        return $this->model->id;
    }

    public function getSmtpHost(): string
    {
        return $this->model->smtp_host;
    }

    public function getSmtpPort(): int
    {
        return $this->model->smtp_port;
    }

    public function getSmtpEncryption(): string
    {
        return $this->model->smtp_encryption;
    }

    public function getNeedAuth(): bool
    {
        return $this->model->auth;
    }

    public function getUsername(): ?string
    {
        return $this->model->smtp_username;
    }

    public function getPassword(): ?string
    {
        return $this->model->smtp_password;
    }
}
