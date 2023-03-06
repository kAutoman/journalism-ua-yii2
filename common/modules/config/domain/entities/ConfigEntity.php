<?php

namespace common\modules\config\domain\entities;

use common\modules\config\infrastructure\values\IField;
use common\modules\config\infrastructure\entities\IConfigEntity;

/**
 * Class ConfigEntity
 *
 * @author Bogdan K. Fedun <delagics@gmail.com>
 */
class ConfigEntity implements IConfigEntity
{
    private $key;
    private $lang;
    private $value;
    private $field = null;
    private $persisted = false;

    public function __construct(string $key, string $lang, $value = null)
    {
        $this->key = $key;
        $this->lang = $lang;
        $this->value = $value;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getLang(): string
    {
        return $this->lang;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value): void
    {
        $this->value = $value;
    }

    public function getField(): ?IField
    {
        return $this->field;
    }

    public function setField(IField $value): void
    {
        $this->field = $value;
    }

    public function getIsPersisted(): bool
    {
        return $this->persisted;
    }

    public function setIsPersisted(bool $value): void
    {
        $this->persisted = $value;
    }

    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        $data = [
            'key' => $this->key,
            'lang' => $this->lang,
            'value' => $this->value,
            'persisted' => $this->persisted,
        ];

        if ($recursive) {
            $data['field'] = optional($this->field)->toArray();
        }

        return merge(array_intersect_key($data, array_flip($fields)), $expand);
    }
}
