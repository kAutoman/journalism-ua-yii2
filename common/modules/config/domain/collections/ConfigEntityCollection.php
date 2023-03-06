<?php

namespace common\modules\config\domain\collections;

use ArrayIterator;
use common\modules\config\domain\exceptions\EntityException;
use common\modules\config\infrastructure\entities\IConfigEntity;
use common\modules\config\infrastructure\collections\IConfigEntityCollection;

/**
 * Class ConfigEntityCollection
 *
 * @author Bogdan K. Fedun <delagics@gmail.com>
 */
class ConfigEntityCollection implements IConfigEntityCollection
{
    private $entities = [];

    public function getIterator()
    {
        return new ArrayIterator($this->entities);
    }

    public function offsetExists($offset)
    {
        return isset($this->entities[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->offsetExists($offset)
            ? $this->entities[$offset]
            : null;
    }

    public function offsetSet($offset, $value)
    {
        if (!$value instanceof IConfigEntity) {
            throw new EntityException('Could not add the config entity to the collection.');
        }

        $this->entities[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            unset($this->entities[$offset]);
        }
    }

    public function count()
    {
        return count($this->entities);
    }

    public function add(IConfigEntity $entity): void
    {
        $this->offsetSet($entity->getKey(), $entity);
    }

    public function remove(string $key): ?IConfigEntity
    {
        $entity = $this->offsetGet($key);
        $this->offsetUnset($key);

        return $entity;
    }

    public function replace(string $oldKey, IConfigEntity $new): void
    {
        $this->offsetUnset($oldKey);
        $this->offsetSet($new->getKey(), $new);
    }

    public function get(string $key): ?IConfigEntity
    {
        return $this->offsetGet($key);
    }

    public function has(string $key): bool
    {
        return $this->offsetExists($key);
    }

    public function clear(): void
    {
        $this->entities = [];
    }

    public function isEmpty(): bool
    {
        return $this->count() <= 0;
    }

    public function each(callable $callback): void
    {
        foreach ($this->entities as $key => $entity) {
            if ($callback($entity, $key) === false) {
                break;
            }
        }
    }

    public function toArray(): array
    {
        return $this->entities;
    }
}
