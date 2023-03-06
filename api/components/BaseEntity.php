<?php

namespace api\components;

use ReflectionException;
use common\modules\builder\models\BuilderModel;

/**
 * Class BaseEntity
 *
 * @package api\components
 */
abstract class BaseEntity
{
    /**
     * @var string
     */
    public $id;
    /**
     * @var array|null
     */
    public $meta;
    /**
     * @var array
     */
    public $blocks;


    /**
     * BaseEntity constructor.
     */
    public function __construct()
    {
        $this->id = $this->getId();
        $this->meta = $this->getMeta();
        $this->blocks();
    }

    /**
     * Unique identifier for entity
     *
     * @return string
     */
    abstract public function getId(): string;

    /**
     * Meta (SEO) data entity
     *
     * @return array|null
     */
    abstract public function getMeta(): ?array;

    /**
     * Base entity content.
     * New blocks can be added using {{@see addBlock()}} method
     *
     * @return void
     */
    abstract public function blocks(): void;

    /**
     * Add new block (component) to response.
     *
     * @param BuilderModel $builderModel
     * @param string|null $blockId
     * @return $this
     * @throws ReflectionException
     */
    public function addBlock(BuilderModel $builderModel, ?string $blockId = null): self
    {
        if ($builderModel->published) {
            $this->blocks[] = [
                'id' => $blockId === null ? $builderModel->getShortName() : $blockId,
                'level' => $builderModel->tag_level,
                'attributes' => $builderModel->getApiAttributes()
            ];
        }

        return $this;
    }
}
