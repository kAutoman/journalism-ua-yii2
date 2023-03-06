<?php

namespace common\modules\mailer\interfaces;

/**
 * Interface IConnection
 *
 * @package common\modules\mailer\interfaces
 */
interface IConnection
{
    public function getId(): int;
    public function getSmtpHost(): string;
    public function getSmtpPort(): int;
    public function getSmtpEncryption(): string;
    public function getNeedAuth(): bool;
    public function getUsername(): ?string;
    public function getPassword(): ?string;
}
