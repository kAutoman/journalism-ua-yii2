<?php

namespace common\modules\config\infrastructure\services;

use common\modules\config\infrastructure\entities\IConfigEntity;
use common\modules\config\infrastructure\collections\IConfigEntityCollection;

/**
 * Interface IConfigManagerService describes service which deals with basic config entities management.
 *
 * @author Bogdan K. Fedun <delagics@gmail.com>
 */
interface IConfigManagerService
{
    public function get(string $key, string $lang): IConfigEntity;

    public function set(string $key, string $lang, $value): void;

    public function has(string $key, string $lang): bool;

    public function all(string $lang): IConfigEntityCollection;

    public function forget(string $key, string $lang): void;

    public function setMultiple(array $values, string $lang): void;
}
