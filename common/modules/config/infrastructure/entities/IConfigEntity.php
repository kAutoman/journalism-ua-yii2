<?php

namespace common\modules\config\infrastructure\entities;

use common\modules\config\infrastructure\values\IField;

/**
 * Interface IConfigEntity is an interface that describes basic configuration entity.
 *
 * @author Bogdan K. Fedun <delagics@gmail.com>
 */
interface IConfigEntity
{
    /**
     * Obtain config entity identifier (key).
     * @return string
     */
    public function getKey(): string;

    /**
     * Obtain config entity language.
     * @return string
     */
    public function getLang(): string;

    /**
     * Obtain config entity value.
     * @return mixed
     */
    public function getValue();

    /**
     * Set config entity value.
     * @param mixed $value value to be set.
     */
    public function setValue($value): void;

    /**
     * Get value that indicates whether entity is already persisted in storage.
     * @return bool
     */
    public function getIsPersisted(): bool;

    /**
     * Set value that indicates whether entity is already persisted in storage.
     * @param bool $value indication of persistence.
     */
    public function setIsPersisted(bool $value): void;

    /**
     * Config entity field object.
     * @return IField
     */
    public function getField(): ?IField;

    /**
     * Set config entity field object.
     * @param IField $value field object to set.
     * @return void
     */
    public function setField(IField $value): void;

    /**
     * Converts the entity into an array.
     * This method made to be compatible with Yii ArrayableTrait [@see \yii\base\ArrayableTrait::toArray()].
     *
     * @param array $fields the fields being requested.
     * @param array $expand the additional fields being requested for exporting.
     * @param bool $recursive whether to recursively return array representation of embedded objects.
     * @return array list of entity properties with their values.
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true);
}
