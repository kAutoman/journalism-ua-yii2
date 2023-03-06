<?php

namespace common\modules\mailer\models;

use common\components\model\ActiveRecord;

/**
 * Class MailerModel
 *
 * @package common\modules\mailer\models
 */
abstract class MailerModel extends ActiveRecord
{
    const STATUS_IN_QUEUE = 0;
    const STATUS_SENDING = 1;
    const STATUS_SUCCESS = 2;
    const STATUS_FAILED = 3;

    /**
     * @return string
     */
    abstract public function getStatusAttribute(): string;

    /**
     * @param int $value
     * @return int
     */
    public function updateStatus(int $value): int
    {
        $statusAttribute = $this->getStatusAttribute();
        $this->$statusAttribute = $value;
        return $this->update(false, [$statusAttribute]);
    }

    /**
     * @return null|string
     */
    public function getStatus(): ?string
    {
        $statusAttribute = $this->getStatusAttribute();
        return $this->getStatusList()[$this->$statusAttribute] ?? null;
    }

    /**
     * @param int $status
     * @return null|string
     */
    public function getStatusLabel(int $status): ?string
    {
        return $this->getStatusList()[$status] ?? null;
    }

    /**
     * @return array
     */
    public function getStatusList(): array
    {
        return [
            self::STATUS_IN_QUEUE => bt('In queue', 'mailer'),
            self::STATUS_SENDING => bt('Sending', 'mailer'),
            self::STATUS_SUCCESS => bt('Success', 'mailer'),
            self::STATUS_FAILED => bt('Failed', 'mailer'),
        ];
    }
}
