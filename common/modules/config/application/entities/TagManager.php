<?php

namespace common\modules\config\application\entities;

use common\modules\config\domain\services\FieldFactory;
use common\modules\config\domain\aggregates\ConfigAggregate;

/**
 * Class TagManager
 *
 * @property string $headEnd
 * @property string $bodyBegin
 * @property string $bodyEnd
 *
 * @package common\modules\config\application\entities
 */
class TagManager extends ConfigAggregate
{
    /**
     * Defines specification for current config entity aggregate.
     * Note: all keys will be auto prefixed with aggregate root name.
     * @return array
     */
    public function specifications(): array
    {
        return [
            bt('Main') => [
                'head.end' => [
                    'type' => FieldFactory::INPUT_CODE,
                    'label' => 'Scripts for head end',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'description' => 'Scripts for head end',
                    'rules' => [
                        ['string', 'max' => 10000],
                    ],
                ],
                'body.begin' => [
                    'type' => FieldFactory::INPUT_CODE,
                    'label' => 'Scripts for body begin',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'description' => 'Scripts for body begin',
                    'rules' => [
                        ['string', 'max' => 10000],
                    ],
                ],
                'body.end' => [
                    'type' => FieldFactory::INPUT_CODE,
                    'label' => 'Scripts for body end',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'description' => 'Scripts for body end',
                    'rules' => [
                        ['string', 'max' => 10000],
                    ],
                ],
            ],
        ];
    }

}
