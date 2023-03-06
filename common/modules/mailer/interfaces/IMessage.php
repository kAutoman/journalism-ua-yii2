<?php

namespace common\modules\mailer\interfaces;

/**
 * Interface IMessage
 *
 * @package common\modules\mailer\interfaces
 */
interface IMessage
{
    public function getSubject(): string;
    public function getTemplate(): string;
    public function getSendFrom(): string;
    public function getSendTo(): string;
    public function getSendToCc(): ?string;
    public function getSendToBcc(): ?string;
    public function getAttachments(): ?string;
}
