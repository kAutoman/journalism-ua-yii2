<?php

namespace common\modules\config\domain\services;

use common\modules\config\infrastructure\entities\IConfigEntity;
use common\modules\config\infrastructure\services\IConfigManagerService;
use common\modules\config\infrastructure\repositories\IStorageRepository;
use common\modules\config\infrastructure\services\IConfigEntityFieldFiller;
use common\modules\config\infrastructure\collections\IConfigEntityCollection;

/**
 * Class ConfigManagerService
 *
 * @author Bogdan K. Fedun <delagics@gmail.com>
 */
class ConfigManagerService implements IConfigManagerService
{
    /**
     * @var IStorageRepository manager repository.
     */
    private $repository;

    /**
     * ConfigManagerService constructor.
     * @param array $specificationSource fields specification source.
     * @param bool $aggregated whether manager is used with aggregated entities.
     * @param IStorageRepository $repository manager repository.
     */
    public function __construct(array $specificationSource, bool $aggregated, IStorageRepository $repository)
    {
        /** @var IConfigEntityFieldFiller $fieldFiller */
        $fieldFiller = createObject(IConfigEntityFieldFiller::class);
        $fieldFiller->setSpecificationSource($specificationSource);
        $fieldFiller->setIsAggregated($aggregated);
        $repository->setFieldFiller($fieldFiller);
        $this->repository = $repository;
    }

    public function get(string $key, string $lang): IConfigEntity
    {
        return $this->repository->find($key, $lang);
    }

    public function set(string $key, string $lang, $value): void
    {
        /** @var IConfigEntity $entity */
        $entity = $this->repository->find($key, $lang);
        $entity->setValue($value);
        $this->repository->save($entity);
    }

    public function setMultiple(array $values, string $lang): void
    {
        if (empty($values)) {
            return;
        }
        /** @var IConfigEntityCollection $collection */
        $collection = $this->repository->findByKeys(array_keys($values), $lang);
        $collection->each(function (IConfigEntity $entity) use ($values, $collection) {
            $newValue = obtain($entity->getKey(), $values, null);
            if ($newValue !== null) {
                $entity->setValue($newValue);
            }
        });

        $this->repository->saveMultiple($collection);
    }

    public function has(string $name, string $lang): bool
    {
        return $this->repository->exist($name, $lang);
    }

    public function forget(string $key, string $lang): void
    {
        /** @var IConfigEntity $entity */
        $entity = $this->repository->find($key, $lang);
        $this->repository->delete($entity);
    }

    public function all(string $lang): IConfigEntityCollection
    {
        return $this->repository->findAllByLang($lang);
    }
}
