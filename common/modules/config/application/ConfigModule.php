<?php

namespace common\modules\config\application;

use yii\base\Module;
use yii\validators\Validator;
use yii\base\BootstrapInterface;
use common\validators\EmailsInStringValidator;
use common\modules\config\domain\values\Field;
use common\modules\config\domain\entities\ConfigEntity;
use common\modules\config\domain\services\FieldFactory;
use common\modules\config\infrastructure\values\IField;
use common\modules\config\domain\services\ConfigManagerService;
use common\modules\config\infrastructure\services\IFieldFactory;
use common\modules\config\infrastructure\entities\IConfigEntity;
use common\modules\config\domain\repositories\StorageRepository;
use common\modules\config\domain\services\ConfigEntityFieldFiller;
use common\modules\config\domain\services\ConfigEntityFormRenderer;
use common\modules\config\domain\collections\ConfigEntityCollection;
use common\modules\config\infrastructure\services\IConfigManagerService;
use common\modules\config\infrastructure\repositories\IStorageRepository;
use common\modules\config\infrastructure\services\IConfigEntityFieldFiller;
use common\modules\config\infrastructure\services\IConfigEntityFormRenderer;
use common\modules\config\infrastructure\collections\IConfigEntityCollection;

/**
 * Class ConfigModule - configuration module class definition.
 * Provides multilingual application configuration via key-value storage
 * and simple way for stored entities creation.
 *
 * To function properly this module should be bootstrapped.
 *
 * @author Bogdan K. Fedun <delagics@gmail.com>
 * @todo improve folder structure.
 */
class ConfigModule extends Module implements BootstrapInterface
{
    /**
     * @var string the default route of this module.
     */
    public $defaultRoute = 'admin/index';
    /**
     * @var string namespace where aggregates resides.
     */
    public $aggregatedEntitiesNamespace = 'common\modules\config\application\entities';
    /**
     * @var array of application config entities specifications.
     */
    private $specifications;

    /**
     * @return array application configuration specification array.
     */
    public function getSpecifications()
    {
        return $this->specifications;
    }

    /**
     * @param array $specifications application configuration specification array.
     * @return void
     */
    public function setSpecifications(array $specifications): void
    {
        $this->specifications = $specifications;
    }

    public function bootstrap($app)
    {
        container()->setDefinitions([
            IField::class => Field::class,
            IFieldFactory::class => FieldFactory::class,
            IConfigEntity::class => ConfigEntity::class,
            IConfigManagerService::class => ConfigManagerService::class,
            IConfigEntityCollection::class => ConfigEntityCollection::class,
            IConfigEntityFieldFiller::class => ConfigEntityFieldFiller::class,
            IConfigEntityFormRenderer::class => ConfigEntityFormRenderer::class,
        ]);
        container()->setSingletons([
            IStorageRepository::class => StorageRepository::class,
        ]);

        $this->registerAdditionalValidators();
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        if(!\Yii::$app->getRequest()->getIsConsoleRequest() && \Yii::$app->id !== 'app-api'){
            ConfigAsset::register(\Yii::$app->view);
        }

        return parent::init();
    }

    /**
     * Register additional validators so that they may be easily accessed via string notation.
     * @return void
     */
    private function registerAdditionalValidators(): void
    {
        Validator::$builtInValidators['emails'] = EmailsInStringValidator::class;
    }
}
