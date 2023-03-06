<?php

namespace common\modules\config\application\entities;

use common\modules\config\domain\services\FieldFactory;

/**
 * Trait SeoAggregateTrait
 *
 * @package common\modules\config\application\entities
 * @property string $metaTitle
 * @property string $metaDescription
 * @property string $metaKeywords
 * @property string $metaImage
 * @author Andrew P. Kontseba <andjey.skinwalker@gmail.com>
 */
trait SeoAggregateTrait
{
    public function additionalSpecifications(): array
    {
        return [
            'Seo' => [
                'meta.title' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Meta title',
                    'default' => '',
                    'display' => true,
                    'rules' => [['required'], ['string', 'max' => 191]],
                ],
                'meta.breadcrumbs.title' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Breadcrumbs title',
                    'default' => '',
                    'display' => true,
                    'rules' => [['required'], ['string', 'max' => 191]],
                ],
                'meta.description' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Meta description',
                    'default' => '',
                    'display' => true,
                    'rules' => [['string', 'max' => 500]],
                ],

                'meta.image' => [
                    'type' => FieldFactory::INPUT_FILE,
                    'label' => 'Seo image',
                    'display' => true,
                    'options' => [
                        'multiple' => false,
                        'allowedFileExtensions' => ['jpeg', 'jpg'],
                        'maxFileSize' => 1024,
                        'metaFields' => true,
                        'webp' => true
                    ],
                ],
                /*
                'meta.keywords' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Meta keywords',
                    'default' => '',
                    'display' => true,
                    'rules' => [['string', 'max' => 500]],
                ],*/
                'meta.canonical' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Canonical URL',
                    'default' => '',
                    'display' => true,
                    'rules' => [['string', 'max' => 500], ['url']],
                ],
                /*'meta.robots' => [
                    'type' => FieldFactory::INPUT_CHECKBOX,
                    'label' => 'robots no index, FOLLOW',
                    'default' => false,
                    'display' => true,
                    'rules' => [['boolean']],
                ],*/
            ]
        ];
    }
}
