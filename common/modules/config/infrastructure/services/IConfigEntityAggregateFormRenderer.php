<?php

namespace common\modules\config\infrastructure\services;

use common\modules\config\infrastructure\values\IField;

/**
 * Interface IConfigEntityAggregateFormRenderer knows how to render aggregate form.
 *
 * @author Bogdan K. Fedun <delagics@gmail.com>
 */
interface IConfigEntityAggregateFormRenderer
{
    /**
     * Obtain aggregate form fields collection.
     * @return IField[]
     */
    public function getFieldsCollection(): array;

    /**
     * Set aggregate form fields collection.
     * @param IField[] $fields
     * @return void
     */
    public function setFieldsCollection(array $fields): void;

    /**
     * Render aggregate form and its fields.
     * @return string
     */
    public function render(): string;
}
