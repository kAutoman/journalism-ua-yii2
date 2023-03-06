<?php

namespace common\modules\config\infrastructure\values;

/**
 * Interface IField describes entity field.
 *
 * @author Bogdan K. Fedun <delagics@gmail.com>
 */
interface IField
{
    /**
     * Entity key name.
     * @return string
     */
    public function getKey(): string;

    /**
     * Entity field name.
     * @return string
     */
    public function getName(): string;

    /**
     * Entity field id.
     * @return string
     */
    public function getInputId(): string;

    /**
     * Entity field type.
     * @return string
     */
    public function getType(): string;

    /**
     * Entity field label.
     * @return string
     */
    public function getLabel(): string;

    /**
     * Entity field description.
     * @return string
     */
    public function getDescription(): string;

    /**
     * Field default value.
     * @return mixed
     */
    public function getDefault();

    /**
     * Field current value.
     * @return mixed
     */
    public function getValue();

    /**
     * Set field current value.
     * @param mixed $value
     * @return void
     */
    public function setValue($value): void;

    /**
     * List of validation rules
     * @return array
     */
    public function getRules(): array;

    /**
     * List of additional field options.
     * @return array
     */
    public function getOptions(): array;

    /**
     * Whether entity field should be shown in generic list.
     * @return bool
     * @todo currently not in use.
     */
    public function getIsDisplayable(): bool;

    /**
     * Whether entity should be auto loaded during every app request.
     * @return bool
     * @todo currently not in use.
     */
    public function getIsAutoloadable(): bool;

    /**
     * Whether field is used together with aggregated entity.
     * @return bool
     */
    public function getIsAggregated(): bool;

    /**
     * Render field to string.
     * @return string
     */
    public function render(): string;

    /**
     * Validate field with its validation rules.
     * @return bool
     * @see getRules()
     */
    public function validate(): bool;

    /**
     * Converts the field into an array.
     * @return array
     */
    public function toArray(): array;
}
