<?php

namespace api\modules\globalData\entities;

use api\components\BaseEntity;
use common\helpers\LanguageHelper;
use common\helpers\MediaHelper;
use common\models\Social;
use common\modules\config\application\components\AggregateMaker;
use common\modules\config\application\entities\Footer;
use common\modules\config\application\entities\TagManager;
use common\modules\menu\models\Menu;
use yii\db\ActiveRecord;

/**
 * Class GlobalDataEntity
 *
 * @package api\modules\globalData\entities
 */
class GlobalDataEntity
{
    public $header;

    public $footer;

    public $scripts;

    public $locales;

    public $contacts;

    public $privacy;

    public $cookie;

    public $errors;

    public $translates;

    /** @var Footer */
    private $footerModel;

    /**
     * GlobalDataEntity constructor.
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function __construct()
    {
        $footerModel = new AggregateMaker(Footer::class);

        $this->footerModel = $footerModel->make();

        $this->setLocales();
        $this->setScripts();
        $this->setHeader();
        $this->setFooter();
        $this->setContact();
        $this->setPrivacy();
        $this->setTranslates();
    }

    /**
     * Sets application available languages
     */
    private function setLocales(): void
    {
        $locales = LanguageHelper::getLanguageModels();
        foreach ($locales as $locale) {
            $this->locales[] = [
                'name' => $locale->label,
                'code' => $locale->code,
                'default' => (bool)$locale->is_default,
                'current' => $locale->code === app()->language
            ];
        }
    }

    private function setScripts()
    {
        $scripsAggregator = new AggregateMaker(TagManager::class);
        /** @var TagManager $tagManager */
        $tagManager = $scripsAggregator->make();

        $this->scripts = [
            'head' => ['end' => $tagManager->headEnd],
            'body' => ['begin' => $tagManager->bodyBegin, 'end' => $tagManager->bodyEnd],
        ];
    }

    public function setHeader()
    {
        $menuItemsQuery = $this->getMenuItems(Menu::LOCATION_HEADER);
        $menuItems = [];
        foreach ($menuItemsQuery as $item) {
            $menuItems[] = [
                'label' => $item->label,
                'url' => $item->getLink()
            ];
        }

        if (!empty($menuItems)) {
            $this->header['links'] = $menuItems;
        }
    }

    /**
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function setFooter()
    {
        $menuItemsQuery = $this->getMenuItems(Menu::LOCATION_FOOTER);

        /** @var Social[] $socials */
        $socials = Social::find()
            ->isPublished()
            ->orderBy(['position' => SORT_ASC])
            ->all();

        $menuItems = [];

        foreach ($menuItemsQuery as $item) {
            $menuItems[] = [
                'label' => $item->label,
                'url' => $item->getLink()
            ];
        }

        if (!empty($menuItems)) {
            $this->footer['links'] = $menuItems;
        }

        foreach ($socials as $social) {
            $this->footer['social'][] = [
                'icon' => MediaHelper::originalSrc($social->iconSrc->file_id ?? null),
                'link' => $social->link,
            ];
        }
    }

    /**
     * @param int $location
     *
     * @return array|Menu[]
     */
    private function getMenuItems(int $location)
    {
        return Menu::find()
            ->andWhere(['location' => $location])
            ->isPublished()
            ->orderBy('position')
            ->limit(8)
            ->all();
    }

    private function setContact()
    {
        $this->contacts['label'] = $this->footerModel->contactLabel;
        $this->contacts['phone'] = $this->footerModel->contactPhone;
        $this->contacts['email'] = $this->footerModel->contactEmail;
        $this->contacts['site']['label'] = $this->footerModel->contactSiteLabel;
        $this->contacts['site']['link'] = $this->footerModel->contactSiteLink;
        $this->contacts['copyright'] = $this->footerModel->contactCopyright;
    }

    /**
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    private function setPrivacy()
    {
        $this->privacy['text'] = $this->footerModel->privacyText;
        $this->privacy['link'] = $this->footerModel->getLink();
        $this->privacy['btn'] = $this->footerModel->getButton();
    }

    private function setTranslates()
    {
        $this->translates['published_at'] = bt('Published date');
    }

    /**
     * Unique identifier for entity
     *
     * @return string
     */
    public function getId(): string
    {
        return '';
    }

    /**
     * Meta (SEO) data entity
     *
     * @return array|null
     */
    public function getMeta(): ?array
    {
        return null;
    }

    /**
     * Base entity content.
     * New blocks can be added using {{@see addBlock()}} method
     *
     * @return void
     */
    public function blocks(): void
    {
    }
}
