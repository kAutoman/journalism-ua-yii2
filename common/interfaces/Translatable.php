<?php

namespace common\interfaces;

/**
 * Interface Translatable
 *
 * @package common\interfaces
 */
interface Translatable
{
    /**
     * List of all translatable attributes from
     *
     * @return array
     */
    public function getLangAttributes(): array;
}
