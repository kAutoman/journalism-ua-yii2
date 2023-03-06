<?php

namespace common\modules\config\application\components;

use common\modules\config\application\ConfigModule;
use common\modules\config\infrastructure\aggregates\IConfigAggregate;
use common\modules\config\infrastructure\services\IConfigManagerService;
use ReflectionClass;

/**
 * Class AggregateMaker
 */
class AggregateMaker
{
    /** @var string */
    private $lang;
    /** @var IConfigManagerService */
    private $manager;
    /** @var IConfigAggregate */
    private $aggregate;

    /**
     * AggregateMaker constructor.
     * @param string $className
     * @param string|null $lang
     * @todo simplify aggregate creation.
     */
    public function __construct(string $className, string $lang = null)
    {
        $this->lang = $lang ?? app()->language;
        $root = strtolower((new ReflectionClass($className))->getShortName());
        $this->aggregate = createObject($className, [$root, $this->lang]);
        $this->manager = createObject(IConfigManagerService::class, [$this->aggregate->getSpecifications(), true]);
    }

    public function make()
    {
        /** @var IConfigAggregate $aggregate */
        $collection = $this->manager->all($this->lang);
        $this->aggregate->setCollection($collection);

        return $this->aggregate;
    }
}
