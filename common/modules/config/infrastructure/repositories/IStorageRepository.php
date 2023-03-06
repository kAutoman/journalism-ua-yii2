<?php

namespace common\modules\config\infrastructure\repositories;

use common\modules\config\infrastructure\entities\IConfigEntity;
use common\modules\config\infrastructure\collections\IConfigEntityCollection;
use common\modules\config\infrastructure\services\IConfigEntityFieldFiller;

/**
 * Interface IStorageRepository describes repository that deals with config entities.
 *
 * @author Bogdan K. Fedun <delagics@gmail.com>
 */
interface IStorageRepository
{
    /**
     * Get field filler service, that knows how to fill entity fields from their specification source.
     * @return IConfigEntityFieldFiller
     */
    public function getFieldFiller(): IConfigEntityFieldFiller;

    /**
     * Set field filler service, that knows how to fill entity fields from their specification source.
     * @param IConfigEntityFieldFiller $value
     * @return void
     */
    public function setFieldFiller(IConfigEntityFieldFiller $value): void;

    /**
     * Find config entity in storage by its key and language.
     * @param string $key config entity key.
     * @param string $lang config entity language.
     * @return IConfigEntity|null
     */
    public function find(string $key, string $lang): IConfigEntity;

    /**
     * Find config entity in storage by specified keys and language.
     * @param array $keys config entity keys.
     * @param string $lang config entity language.
     * @return IConfigEntityCollection
     */
    public function findByKeys(array $keys, string $lang): IConfigEntityCollection;

    /**
     * Check whether entity exist in storage, by its key and language.
     * @param string $key config entity key.
     * @param string $lang config entity language.
     * @return bool
     */
    public function exist(string $key, string $lang): bool;

    /**
     * Save config entity in storage.
     * @param IConfigEntity $entity config entity to be saved.
     * @return void
     */
    public function save(IConfigEntity $entity): void;

    /**
     * Delete config entity in storage.
     * @param IConfigEntity $entity entity to bbe deleted.
     * @return void
     */
    public function delete(IConfigEntity $entity): void;

    /**
     * Delete all config entities in storage, that are specified by field filler.
     * @param string $lang entities language.
     * @return void
     */
    public function deleteAll(string $lang): void;

    /**
     * Save config entity collection in storage.
     * @param IConfigEntityCollection $collection collection to be saved.
     * @return void
     */
    public function saveMultiple(IConfigEntityCollection $collection): void;

    /**
     * Find all config entities by their language.
     * @param string $lang entities language.
     * @return IConfigEntityCollection
     */
    public function findAllByLang(string $lang): IConfigEntityCollection;

    /**
     * Find all config entities by their language, which key names starts with specified string.
     * @param string $start key name part, from the beginning.
     * @param string $lang entities language.
     * @return IConfigEntityCollection
     */
    // public function findAllStartingWithByLang(string $start, string $lang): IConfigEntityCollection;

    /**
     * Delete all config entities by their language and which key names starts with specified string.
     * @param string $start key name part, from the beginning.
     * @param string $lang entities language.
     * @return void
     */
    // public function deleteAllStartingWithByLang(string $start, string $lang): void;
}
