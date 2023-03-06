<?php

namespace common\modules\config\infrastructure\collections;

use Countable;
use ArrayAccess;
use IteratorAggregate;
use common\modules\config\infrastructure\entities\IConfigEntity;

/**
 * Interface IConfigEntityCollection characterize plain config entity collection.
 *
 * @author Bogdan K. Fedun <delagics@gmail.com>
 */
interface IConfigEntityCollection extends ArrayAccess, Countable, IteratorAggregate
{
    /**
     * Add entity to collection.
     * @param IConfigEntity $entity
     * @return void
     */
    public function add(IConfigEntity $entity): void;

    /**
     * Remove entity from collection by its key.
     * @param string $key config entity key.
     * @return IConfigEntity|null
     */
    public function remove(string $key): ?IConfigEntity;

    /**
     * Replace old entity (by key) with new one.
     * @param string $oldKey old config entity key.
     * @param IConfigEntity $new new config entity object.
     * @return void
     */
    public function replace(string $oldKey, IConfigEntity $new): void;

    /**
     * Obtain entity by its key.
     * @param string $key
     * @return IConfigEntity|null
     */
    public function get(string $key): ?IConfigEntity;

    /**
     * Check whether entity exists in collection by its key.
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * Clear all entities from collection.
     * @return void
     */
    public function clear(): void;

    /**
     * Check whether collection is empty.
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * Execute a callback over each item.
     *
     * @param callable $callback
     * @return void
     */
    public function each(callable $callback): void;

    /**
     * Transform entity collection to simple array.
     * @return array
     */
    public function toArray(): array;
}
