<?php

namespace common\modules\dynamicForm\interfaces;

/**
 * Class DynamicFormInterface
 *
 * @package common\modules\dynamicForm\interfaces
 */
interface DynamicFormInterface
{
    /**
     * Configuration for Dynamic Form entities
     *
     * @return array
     */
    public function getDynamicFormConfig(): ?array;
}
