<?php

namespace common\modules\config\domain\repositories;

use yii\db\Query;
use common\modules\config\infrastructure\entities\IConfigEntity;
use common\modules\config\infrastructure\repositories\IStorageRepository;
use common\modules\config\infrastructure\services\IConfigEntityFieldFiller;
use common\modules\config\infrastructure\collections\IConfigEntityCollection;
use common\modules\config\domain\exceptions\UndefinedFieldSpecificationException;
use yii\helpers\Json;

/**
 * Class StorageRepository
 *
 * @author Bogdan K. Fedun <delagics@gmail.com>
 */
class StorageRepository implements IStorageRepository
{
    /** @var string table name. */
    public $table = '{{%config}}';

    /** @var IConfigEntityFieldFiller */
    private $fieldFiller;

    public function getFieldFiller(): IConfigEntityFieldFiller
    {
        return $this->fieldFiller;
    }

    public function setFieldFiller(IConfigEntityFieldFiller $value): void
    {
        $this->fieldFiller = $value;
    }

    public function find(string $key, string $lang): IConfigEntity
    {
        $entity = $this->getFieldFiller()->makeEntity($key, $lang);
        $value = (new Query())->select(['value'])->from($this->table)->where(compact('key', 'lang'))->scalar();

        if ($value) {
            $entity->setValue($value);
            $entity->setIsPersisted(true);
        }

        return $entity;
    }

    public function findAllByLang(string $lang): IConfigEntityCollection
    {
        $collection = $this->getFieldFiller()->makeEntities($lang);

        $rows = (new Query())->from($this->table)->where([
            'key' => array_keys($collection->toArray()),
            'lang' => $lang,
        ])->indexBy('key')->all();

        $collection->each(function (IConfigEntity $entity) use ($rows) {
            $value = obtain([$entity->getKey(), 'value'], $rows);
            if ($value) {
                $entity->setValue($value);
                $entity->setIsPersisted(true);
                $entity->getField()->setValue($value);
            }
        });

        return $collection;
    }

    public function findByKeys(array $keys, string $lang): IConfigEntityCollection
    {
        $collection = $this->getFieldFiller()->makeEntitiesForKeys($keys, $lang);
        $values = (new Query())->select(['key', 'value'])
            ->from($this->table)
            ->where(['key' => $keys, 'lang' => $lang])
            ->indexBy('key')->all();

        $collection->each(function (IConfigEntity $entity) use ($values) {
            $value = obtain([$entity->getKey(), 'value'], $values, null);
            $entity->setValue($value);
            $value === null
                ? $entity->setIsPersisted(false)
                : $entity->setIsPersisted(true);
            $entity->getField()->setValue($value);
        });

        return $collection;
    }

    /**
     * {@inheritdoc}
     * @throws UndefinedFieldSpecificationException
     * @throws \yii\db\Exception
     */
    public function save(IConfigEntity $entity): void
    {
        $specificationExist = $this->fieldFiller->specificationExist($entity->getKey());

        if (!$specificationExist) {
            throw new UndefinedFieldSpecificationException();
        }

        $command = app()->getDb()->createCommand();
        if ($entity->getIsPersisted()) {
            $command->update($this->table, $entity->toArray(['value']), [
                'key' => $entity->getKey(),
                'lang' => $entity->getLang(),
            ])->execute();

            app()->trigger(CLEAR_CACHE);

            return;
        }

        $command->insert($this->table, $entity->toArray(['key', 'lang', 'value']))->execute();

        app()->trigger(CLEAR_CACHE);
    }

    public function saveMultiple(IConfigEntityCollection $collection): void
    {

        foreach ($collection as $entity) {
            $this->prepareFieldsEntity($entity);
            $this->save($entity);
        }
    }

    public function exist(string $key, string $lang): bool
    {
        return (new Query())->from($this->table)->where(compact('key', 'lang'))->exists();
    }

    public function delete(IConfigEntity $entity): void
    {
        app()->getDb()->createCommand()
            ->delete($this->table, ['key' => $entity->getKey(), 'lang' => $entity->getLang()])
            ->execute();
    }

    public function deleteAll(string $lang): void
    {
        $keys = [];
        $collection = $this->getFieldFiller()->makeEntities($lang);
        /** @var IConfigEntity $entity */
        foreach ($collection as $entity) {
            $keys[] = $entity->getKey();
        }
        if (!empty($keys)) {
            app()->getDb()->createCommand()
                ->delete($this->table, ['key' => $keys, 'lang' => $lang])
                ->execute();
        }
    }

    protected function prepareFieldsEntity(IConfigEntity &$entity): void
    {
        if(is_array($entity->getValue())){
            $entity->setValue(Json::encode(array_values($entity->getValue())));
        }
    }
}
