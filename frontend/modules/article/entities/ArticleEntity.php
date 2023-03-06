<?php

namespace frontend\modules\article\entities;

use api\components\BaseEntity;
use frontend\modules\article\models\Article;
use api\modules\page\models\Page;
use common\helpers\LanguageHelper;
use common\helpers\UrlHelper;
use common\modules\config\application\components\AggregateMaker;
use common\modules\seo\helpers\MetaTagHelper;

/**
 * Class ArticleEntity
 * @package api\modules\article\entities
 */
class ArticleEntity extends BaseEntity
{
    /**
     * @var Article
     */
    private $page;

    /** @var Page|null */
    private $mainPage;

    public function __construct(Article $page)
    {
        $this->page = $page;

        $this->mainPage = Page::find()->andWhere([
            'alias' => 'news',
            'published' => true,
        ])->one();

        parent::__construct();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return 'article';
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
            'pageOptions' => [
                'date' => formatter()->asDate($this->page->publication_date, 'php:d.m.Y'),
                'banner' => formatter()->image($this->page->banner->file_id ?? null),
            ]
        ];
    }

    /**
     * @throws \ReflectionException
     */
    public function blocks(): void
    {
        foreach ($this->page->builderContent as $builderModel) {
            $this->addBlock($builderModel);
        }
    }

    private function getBreadcrumbs()
    {
        $breadcrumbs = [];

        if ($this->mainPage) {
            $breadcrumbs[] = [
                'label' => $this->mainPage->label ?? null,
                'url' => UrlHelper::toRoute(['/page/page/index', 'alias' => 'news']),
            ];
        }

        $breadcrumbs[] = [
            'label' => $this->page->label,
            'url' => self::getPublicUrl(['alias' => $this->page->alias]),
        ];

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
        return UrlHelper::createUrl('/article/article/view', $params, $schema);
    }
}
