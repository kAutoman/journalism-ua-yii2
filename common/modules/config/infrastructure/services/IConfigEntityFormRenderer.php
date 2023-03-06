<?php

namespace common\modules\config\infrastructure\services;

use common\modules\config\infrastructure\collections\IConfigEntityCollection;

/**
 * Interface IConfigEntityFormRenderer knows how to render entity form.
 *
 * @author Bogdan K. Fedun <delagics@gmail.com>
 */
interface IConfigEntityFormRenderer
{
    /**
     * Obtain form entities.
     * @return IConfigEntityCollection
     */
    public function getEntities(): IConfigEntityCollection;

    /**
     * Set form entities.
     * @param IConfigEntityCollection $entities
     * @return void
     */
    public function setEntities(IConfigEntityCollection $entities): void;

    /**
     * Set form validation URL.
     * @param array $value
     */
    public function setValidationUrl(array $value): void;

    /**
     * Get form validation URL.
     * @return string
     */
    public function getValidationUrl(): string;

    /**
     * Render entity form and its fields.
     * @return string
     */
    public function renderForm(): string;
}
