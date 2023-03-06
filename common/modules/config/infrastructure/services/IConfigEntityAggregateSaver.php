<?php

namespace common\modules\config\infrastructure\services;

use common\modules\config\domain\exceptions\AggregateException;
use common\modules\config\infrastructure\aggregates\IConfigAggregate;

/**
 * Interface IConfigEntityAggregateSaver represents a service which knows how to store aggregate of config entities.
 *
 * @author Bogdan K. Fedun <delagics@gmail.com>
 */
interface IConfigEntityAggregateSaver
{
    /**
     * Save aggregate in storage.
     * @param IConfigAggregate $entity entity to be saved.
     * @throws AggregateException when aggregate cannot be saved.
     */
    public function save(IConfigAggregate $entity): void;
}
