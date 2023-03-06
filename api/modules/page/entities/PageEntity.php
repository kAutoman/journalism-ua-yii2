<?php

namespace api\modules\page\entities;

use api\components\BaseEntity;
use api\modules\page\models\Page;
use common\helpers\LanguageHelper;
use common\helpers\UrlHelper;
use common\modules\builder\blocks\Editor;
use common\modules\seo\helpers\MetaTagHelper;
use paulzi\nestedsets\NestedSetsBehavior;

/**
 * Class PageEntity
 *
 * @package api\modules\page\entities
 */
class PageEntity extends BaseEntity
{
    /**
     * @var Page|NestedSetsBehavior
     */
    private $page;

    public function __construct(Page $page)
    {
        $this->page = $page;

        parent::__construct();
    }

    public function getId(): string
    {
        return $this->page->entity_id;
    }

    /**
     * @return array|null
     * @throws \Exception
     */
    public function getMeta(): ?array
    {
        return [
            'seo' => array_merge(
                MetaTagHelper::register($this->page),
                ['breadcrumbs' => $this->getBreadcrumbs()]
            ),
            'locales' => $this->getAlternates(),
            'pageOptions' => []
        ];
    }

    /**
     * @throws \ReflectionException
     */
    public function blocks(): void
    {
        if ($this->page->type === Page::TYPE_BASIC) {
            $editor = new Editor(['content' => $this->page->content, 'published' => true]);
            $this->addBlock($editor, 'Editor');
        } else {
            foreach ($this->page->builderContent as $builderModel) {
                $this->addBlock($builderModel);
            }
        }
    }

    private function getBreadcrumbs()
    {
        /** @var Page[] $parents */
        $parents = $this->page->getParents()->all();
        $breadcrumbs = [];
        foreach ($parents as $parent) {
            $breadcrumbs[] = [
                'label' => $parent->label,
                'url' => $parent->isHome() ? '/' : self::getPublicUrl(['alias' => $parent->alias]),
            ];
        }
        // add current page
        array_push($breadcrumbs, [
            'label' => $this->page->label,
            'url' => $this->page->isHome() ? '/' : self::getPublicUrl(['alias' => $this->page->alias])
        ]);

        return $breadcrumbs;
    }

    private function getAlternates()
    {
        $alternates = [];
        $locales = LanguageHelper::getLanguageModels();
        foreach ($locales as $locale) {
            $alternates[] = [
                'name' => $locale->label,
                'code' => $locale->code,
                'default' => (bool)$locale->is_default,
                'current' => $locale->code === app()->language,
                'url' => self::getPublicUrl(['alias' => $this->page->alias, '_lang' => $locale->code], true)
            ];
        }
        return $alternates;
    }

    /**
     * @param array $params
     * @param bool $schema
     *
     * @return string
     */
    public static function getPublicUrl($params = [], bool $schema = false)
    {
        return UrlHelper::createUrl('/page/page/index', $params, $schema);
    }
}
