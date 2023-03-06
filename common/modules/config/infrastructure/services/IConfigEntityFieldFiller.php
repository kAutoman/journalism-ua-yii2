<?php

namespace common\modules\config\infrastructure\services;

use common\modules\config\infrastructure\entities\IConfigEntity;
use common\modules\config\infrastructure\collections\IConfigEntityCollection;
use common\modules\config\domain\exceptions\UndefinedFieldSpecificationException;

/**
 * Interface IConfigEntityFieldFiller
 *
 * @author Bogdan K. Fedun <delagics@gmail.com>
 */
interface IConfigEntityFieldFiller
{
    /**
     * Get specifications source.
     * @return array
     */
    public function getSpecificationSource(): array;

    /**
     * Set specifications source.
     * @param array $value specifications source.
     * @return void
     */
    public function setSpecificationSource(array $value): void;

    /**
     * Find specification for specified field key.
     * @param string $key field key(name).
     * @return array
     * @throws UndefinedFieldSpecificationException when specification is not found for specified field.
     */
    public function findSpecification(string $key): array;

    /**
     * Make field object from specification.
     * @param IConfigEntity $entity field key(name).
     * @return void
     * @throws UndefinedFieldSpecificationException when specification is not found for specified entity.
     */
    public function fillField(IConfigEntity $entity): void;

    /**
     * Make entity from specification source.
     * @param string $key entity key.
     * @param string $lang entity language.
     * @return IConfigEntity
     */
    public function makeEntity(string $key, string $lang): IConfigEntity;

    /**
     * Make entities from specification source.
     * @param string $lang entities language.
     * @return IConfigEntityCollection
     */
    public function makeEntities(string $lang): IConfigEntityCollection;

    /**
     * Make entities from specification source by specified keys.
     * @param array $keys entities keys.
     * @param string $lang entities language.
     * @return IConfigEntityCollection
     */
    public function makeEntitiesForKeys(array $keys, string $lang): IConfigEntityCollection;

    /**
     * Check whether specification of entity exist.
     * @param string $key entity key.
     * @return bool
     */
    public function specificationExist(string $key): bool;

    /**
     * Return value indicating whether this class is used with aggregated entities.
     * @return bool
     */
    public function getIsAggregated(): bool;

    /**
     * Set value indicating whether this class is used with aggregated entities.
     * @param bool $value
     */
    public function setIsAggregated(bool $value): void;
}
