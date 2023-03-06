<?php

namespace common\modules\config\infrastructure\aggregates;

use common\modules\config\domain\exceptions\AggregateException;
use common\modules\config\infrastructure\collections\IConfigEntityCollection;

/**
 * Interface IConfigAggregate describes object that
 * aggregates another entities in itself and behaves like simple entity.
 *
 * @author Bogdan K. Fedun <delagics@gmail.com>
 */
interface IConfigAggregate
{
    /**
     * Get config aggregate identifier (root).
     * Commonly it is a first generic part of aggregated entities identifiers (keys).
     * e.g. if aggregate contains entities with keys: 'app.name', 'app.version', 'app.author',
     * then aggregate root should be 'app'.
     * @return string
     */
    public function getRoot(): string;

    /**
     * Get config aggregate language.
     * @return string
     */
    public function getLang(): string;

    /**
     * Aggregate properties specifications.
     * @return array
     */
    public function getSpecifications(): array;

    /**
     * Return a collection of aggregated entities.
     * @return IConfigEntityCollection
     */
    public function getCollection(): IConfigEntityCollection;

    /**
     * Set a collection of aggregated entities.
     * @param IConfigEntityCollection $value
     * @return void
     */
    public function setCollection(IConfigEntityCollection $value): void;

    /**
     * Magic method, adds ability to access aggregated configuration values as current object properties.
     * @param string $name configuration key, without aggregate root.
     * @return mixed
     * @throws AggregateException
     */
    public function __get($name);

    /**
     * Magic method, adds ability to set aggregated configuration values via current object properties.
     * @param string $name configuration key, without aggregate root.
     * @param mixed $value
     * @return void
     * @throws AggregateException
     */
    public function __set($name, $value): void;
}
