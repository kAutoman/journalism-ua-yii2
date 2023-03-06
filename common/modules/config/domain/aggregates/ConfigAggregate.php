<?php

namespace common\modules\config\domain\aggregates;

use common\modules\config\domain\services\FieldFactory;
use common\modules\config\infrastructure\aggregates\IConfigAggregate;
use common\modules\config\infrastructure\collections\IConfigEntityCollection;
use yii\helpers\Inflector;

/**
 * Class ConfigAggregate
 *
 * @author Bogdan K. Fedun <delagics@gmail.com>
 */
abstract class ConfigAggregate implements IConfigAggregate
{
    /**
     * @var string aggregate identifier (root).
     */
    private $root;
    /**
     * @var string aggregate language.
     */
    private $lang;
    /**
     * @var array list of config entity aggregate specifications.
     */
    private $specifications;
    /**
     * @var IConfigEntityCollection collection of aggregated entities.
     */
    private $collection;

    public function __construct(string $root, string $lang)
    {
        $this->root = $root;
        $this->lang = $lang;
    }

    public function getRoot(): string
    {
        return $this->root;
    }

    public function getLang(): string
    {
        return $this->lang;
    }

    /**
     * Defines specification for current config entity aggregate.
     * Note: all keys will be auto prefixed with aggregate root name.
     * @return array
     */
    abstract public function specifications(): array;

    /**
     * Additional specifications for current config entity aggregate.
     * Note: can be used for create seo.
     * @return array
     */
    public function additionalSpecifications(): array
    {
        return [];
    }

    public function getSpecifications(): array
    {
        if ($this->specifications === null) {
            $specifications = merge($this->specifications(), $this->additionalSpecifications());
            foreach ($specifications as $tab => $items) {
                foreach ($items as $name => $specification) {
                    $specification['tab'] = $tab;
                    $this->specifications["{$this->root}.$name"] = $specification;
                }
            }
        }

        return $this->specifications;
    }

    public function getCollection(): IConfigEntityCollection
    {
        return $this->collection;
    }

    public function setCollection(IConfigEntityCollection $value): void
    {
        $this->collection = $value;
    }

    public function __get($name)
    {
        $key = $this->getRoot() . '.' . Inflector::camel2id($name, '.');
        if ($this->collection->has($key)) {
            return $this->collection->get($key)->getValue();
        }
    }

    public function __set($name, $value): void
    {
        $entity = $this->collection->get($name);
        $entity->setValue($value);
        $this->collection->replace($entity, $entity);
    }
}
