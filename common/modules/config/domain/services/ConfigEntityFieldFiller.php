<?php

namespace common\modules\config\domain\services;

use common\modules\config\infrastructure\values\IField;
use common\modules\config\infrastructure\entities\IConfigEntity;
use common\modules\config\infrastructure\services\IFieldFactory;
use common\modules\config\infrastructure\services\IConfigEntityFieldFiller;
use common\modules\config\infrastructure\collections\IConfigEntityCollection;
use common\modules\config\domain\exceptions\UndefinedFieldSpecificationException;

/**
 * Class ConfigEntityFieldFiller
 *
 * @author Bogdan K. Fedun <delagics@gmail.com>
 */
class ConfigEntityFieldFiller implements IConfigEntityFieldFiller
{
    /**
     * @var array specifications source.
     */
    private $source = [];
    /**
     * @var bool whether this class is used with aggregated entities.
     */
    private $aggregated;

    public function getSpecificationSource(): array
    {
        return $this->source;
    }

    public function setSpecificationSource($source): void
    {
        $this->source = $source;
    }

    public function findSpecification(string $key): array
    {
        $specification = obtain($key, $this->source, false);

        if ($specification === false) {
            throw new UndefinedFieldSpecificationException();
        }

        return $specification;
    }

    public function fillField(IConfigEntity $entity): void
    {
        $specification = $this->findSpecification($entity->getKey());
        /** @var IFieldFactory $factory */
        $factory = createObject(IFieldFactory::class);
        /** @var IField $field */
        $field = $factory->make($entity->getKey(), $specification, $this->getIsAggregated());
        $entity->setField($field);
    }

    /**
     * {@inheritdoc}
     * @throws UndefinedFieldSpecificationException
     */
    public function makeEntities(string $lang): IConfigEntityCollection
    {
        $collection = createObject(IConfigEntityCollection::class);

        foreach ($this->getSpecificationSource() as $key => $specification) {
            $entity = $this->makeEntity($key, $lang);
            $collection->add($entity);
        }

        return $collection;
    }

    /**
     * {@inheritdoc}
     * @throws UndefinedFieldSpecificationException
     */
    public function makeEntitiesForKeys(array $keys, string $lang): IConfigEntityCollection
    {
        $collection = createObject(IConfigEntityCollection::class);


        foreach ($keys as $key) {
            $entity = $this->makeEntity($key, $lang);
            $collection->add($entity);
        }

        return $collection;
    }

    /**
     * @inheritdoc
     * @throws UndefinedFieldSpecificationException
     */
    public function makeEntity(string $key, string $lang): IConfigEntity
    {
        $specification = $this->findSpecification($key);
        $defaultValue = obtain('default', $specification, null);
        /** @var IConfigEntity $entity */
        $entity = createObject(IConfigEntity::class, [$key, $lang, $defaultValue]);
        $this->fillField($entity);

        return $entity;
    }

    public function specificationExist(string $key): bool
    {
        return isset($this->source[$key]);
    }

    /**
     * Return value indicating whether this class is used with aggregated entities.
     * @return bool
     */
    public function getIsAggregated(): bool
    {
        return $this->aggregated;
    }

    /**
     * Set value indicating whether this class is used with aggregated entities.
     * @param bool $value
     */
    public function setIsAggregated(bool $value): void
    {
        $this->aggregated = $value;
    }
}
