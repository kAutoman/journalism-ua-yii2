<?php

namespace common\modules\config\application\entities;

use common\helpers\MediaHelper;
use common\modules\config\domain\aggregates\ConfigAggregate;
use common\modules\config\domain\services\FieldFactory;

/**
 * Class Footer
 *
 * @package common\modules\config\application\entities
 *
 * @property string $contactLabel
 * @property string $contactPhone
 * @property string $contactEmail
 *
 * @property string $contactSiteLabel
 * @property string $contactSiteLink
 *
 * @property string $contactCopyright
 *
 * @property string $privacyText
 *
 * @property string $privacyLinkLabel
 * @property string $privacyLinkUrl
 * @property string $privacyLinkFile
 *
 * @property string $privacyBtnLabel
 * @property string $privacyBtnUrl
 * @property string $privacyBtnFile
 */
class Footer extends ConfigAggregate
{
    /**
     * Defines specification for current config entity aggregate.
     * Note: all keys will be auto prefixed with aggregate root name.
     *
     * @return array
     */
    public function specifications(): array
    {
        return [
            bt('Contact', 'footer') => [
                'contact.label' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Name label',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['string', 'max' => 255],
                    ],
                ],
                'contact.phone' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Phone',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['string', 'max' => 255],
                    ],
                ],
                'contact.email' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Email',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['string', 'max' => 255],
                    ],
                ],
                'contact.site.label' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Site label',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['string', 'max' => 255],
                    ],
                ],
                'contact.site.link' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Site link',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['string', 'max' => 255],
                    ],
                ],
                'contact.copyright' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Copyright',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['string', 'max' => 255],
                    ],
                ],
            ],
            bt('Privacy', 'footer') => [
                'privacy.text' => [
                    'type' => FieldFactory::INPUT_TEXTAREA,
                    'label' => 'Text',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['string', 'max' => 5000],
                    ],
                ],
                'privacy.link.label' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Link label',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['string', 'max' => 255],
                    ],
                ],
                'privacy.link.url' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Link url',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['string', 'max' => 255],
                    ],
                ],
                'privacy.link.file' => [
                    'type' => FieldFactory::INPUT_FILE,
                    'label' => 'Link file',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'options' => [
                        'multiple' => false,
                        'allowedFileExtensions' => ['pdf'],
                        'maxFileSize' => MAX_DOC_KB,
                        'metaFields' => true,
                        'webp' => true
                    ],
                ],
                'privacy.btn.label' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Button label',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['string', 'max' => 255],
                    ],
                ],
                'privacy.btn.url' => [
                    'type' => FieldFactory::INPUT_TEXT,
                    'label' => 'Button url',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'rules' => [
                        ['string', 'max' => 255],
                    ],
                ],
                'privacy.btn.file' => [
                    'type' => FieldFactory::INPUT_FILE,
                    'label' => 'Button file',
                    'default' => null,
                    'display' => true,
                    'autoload' => true,
                    'options' => [
                        'multiple' => false,
                        'allowedFileExtensions' => ['pdf'],
                        'maxFileSize' => MAX_DOC_KB,
                        'metaFields' => true,
                        'webp' => true
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function getLink(): array
    {
        if ($this->privacyLinkFile) {
            return [
                'label' => $this->privacyLinkLabel,
                'url' => MediaHelper::originalSrc($this->privacyLinkFile),
            ];
        } else {
            return [
                'label' => $this->privacyLinkLabel,
                'url' => $this->privacyLinkUrl,
            ];
        }
    }

    /**
     * @return array
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function getButton(): array
    {
        if ($this->privacyBtnFile) {
            return [
                'label' => $this->privacyBtnLabel,
                'url' => MediaHelper::originalSrc($this->privacyBtnFile),
            ];
        } else {
            return [
                'label' => $this->privacyBtnLabel,
                'url' => $this->privacyBtnUrl,
            ];
        }
    }
}
