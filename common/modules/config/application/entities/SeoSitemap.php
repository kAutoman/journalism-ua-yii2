<?php

namespace common\modules\config\application\entities;

use common\modules\config\domain\services\FieldFactory;
use common\modules\config\domain\aggregates\ConfigAggregate;

/**
 * Class SeoSitemap
 * @package common\modules\config\application\entities
 * @author Andrew P. Kontseba <andjey.skinwalker@gmail.com>
 * @deprecated
 */
class SeoSitemap extends ConfigAggregate
{
    /**
     * Defines specification for current config entity aggregate.
     * Note: all keys will be auto prefixed with aggregate root name.
     * @return array
     */
    public function specifications(): array
    {
        return [
            'Static pages' => [
                'freq.home' => [
                    'type' => FieldFactory::INPUT_DROPDOWN_LIST,
                    'label' => 'Home freq',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 10000],
                    ],
                    'options' => [
                        'items' => $this->freqList(),
                    ]
                ],
                'priority.home' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Home priority',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 255],
                    ],
                ],
                'freq.about' => [
                    'type' => FieldFactory::INPUT_DROPDOWN_LIST,
                    'label' => 'About freq',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 10000],
                    ],
                    'options' => [
                        'items' => $this->freqList(),
                    ]
                ],
                'priority.about' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'About priority',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 255],
                    ],
                ],
                'freq.services' => [
                    'type' => FieldFactory::INPUT_DROPDOWN_LIST,
                    'label' => 'Services freq',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 10000],
                    ],
                    'options' => [
                        'items' => $this->freqList(),
                    ]
                ],
                'priority.services' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Services priority',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 255],
                    ],
                ],
                'freq.portfolio' => [
                    'type' => FieldFactory::INPUT_DROPDOWN_LIST,
                    'label' => 'Portfolio freq',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 10000],
                    ],
                    'options' => [
                        'items' => $this->freqList(),
                    ]
                ],
                'priority.portfolio' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Portfolio priority',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 255],
                    ],
                ],
                'freq.blog' => [
                    'type' => FieldFactory::INPUT_DROPDOWN_LIST,
                    'label' => 'Blog freq',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 10000],
                    ],
                    'options' => [
                        'items' => $this->freqList(),
                    ]
                ],
                'priority.blog' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Blog priority',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 255],
                    ],
                ],
                'freq.career' => [
                    'type' => FieldFactory::INPUT_DROPDOWN_LIST,
                    'label' => 'Career freq',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 10000],
                    ],
                    'options' => [
                        'items' => $this->freqList(),
                    ]
                ],
                'priority.career' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Career priority',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 255],
                    ],
                ],
                'freq.contacts' => [
                    'type' => FieldFactory::INPUT_DROPDOWN_LIST,
                    'label' => 'Contacts freq',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 10000],
                    ],
                    'options' => [
                        ['required'],
                        'items' => $this->freqList(),
                    ]
                ],
                'priority.contacts' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Contacts priority',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 255],
                    ],
                ],
                'freq.policy' => [
                    'type' => FieldFactory::INPUT_DROPDOWN_LIST,
                    'label' => 'Policy freq',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 10000],
                    ],
                    'options' => [
                        'items' => $this->freqList(),
                    ]
                ],
                'priority.policy' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Policy priority',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 255],
                    ],
                ],
            ],
            'Generated pages' => [
                'freq.single.service' => [
                    'type' => FieldFactory::INPUT_DROPDOWN_LIST,
                    'label' => 'Single service freq',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 10000],
                    ],
                    'options' => [
                        'items' => $this->freqList(),
                    ]
                ],
                'priority.single.service' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Single service priority',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 255],
                    ],
                ],
                'freq.single.case' => [
                    'type' => FieldFactory::INPUT_DROPDOWN_LIST,
                    'label' => 'Single case freq',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 10000],
                    ],
                    'options' => [
                        'items' => $this->freqList(),
                    ]
                ],
                'priority.single.case' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Single case priority',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 255],
                    ],
                ],
                'freq.single.vacancy' => [
                    'type' => FieldFactory::INPUT_DROPDOWN_LIST,
                    'label' => 'Single vacancy freq',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 10000],
                    ],
                    'options' => [
                        'items' => $this->freqList(),
                    ]
                ],
                'priority.single.vacancy' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Single vacancy priority',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 255],
                    ],
                ],
                'freq.single.article' => [
                    'type' => FieldFactory::INPUT_DROPDOWN_LIST,
                    'label' => 'Single article freq',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 10000],
                    ],
                    'options' => [
                        'items' => $this->freqList(),
                    ]
                ],
                'priority.single.article' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Single article priority',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['required'],
                        ['string', 'max' => 255],
                    ],
                ],
            ],
        ];
    }

    public function freqList()
    {
        return [
            'always' => 'always',
            'hourly' => 'hourly',
            'daily' => 'daily',
            'weekly' => 'weekly',
            'monthly' => 'monthly',
            'yearly' => 'yearly',
            'never' => 'never',
        ];
    }
}
