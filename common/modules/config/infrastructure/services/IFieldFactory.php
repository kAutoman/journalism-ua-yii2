<?php

namespace common\modules\config\infrastructure\services;

use common\modules\config\infrastructure\values\IField;

/**
 * Interface IFieldFactory
 *
 * @author Bogdan K. Fedun <delagics@gmail.com>
 */
interface IFieldFactory
{
    /**
     * Make field object.
     * @param string $name field name.
     * @param array $specification field specification.
     * @param bool $aggregated whether this field is used with aggregated entity.
     * @return IField
     */
    public function make(string $name, array $specification, bool $aggregated = false): IField;
}
