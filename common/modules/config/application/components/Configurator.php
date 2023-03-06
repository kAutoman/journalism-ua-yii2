<?php

namespace common\modules\config\application\components;

use yii\base\Component;
use common\modules\config\application\ConfigModule;
use common\modules\config\infrastructure\services\IConfigManagerService;
use common\modules\config\infrastructure\collections\IConfigEntityCollection;

/**
 * Class Configurator
 */
class Configurator extends Component
{
    /**
     * @var string current language.
     */
    private $lang;
    /**
     * @var IConfigManagerService knows how to work with specifications.
     */
    private $manager;
    /**
     * @var IConfigEntityCollection configuration collection.
     */
    private $collection;

    /**
     * Configurator constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->lang = app()->language;
        /** @var ConfigModule $module */
        $module = app()->getModule('config');
        $this->manager = createObject(IConfigManagerService::class, [$module->getSpecifications(), false]);
        $this->collection = $this->manager->all($this->lang);
        parent::__construct($config);
    }

    public function get($key, $default = null)
    {
        $entity = $this->collection->get($key);

        return $entity ? $entity->getValue() : $default;
    }
}
