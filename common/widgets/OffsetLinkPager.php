<?php

namespace common\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use frontend\components\OffsetPagination;

/**
 * OffsetLinkPager displays a hyperlink that lead to different page of target.
 *
 * @package common\widgets
 * @author Bogdan Fedun <delagics@gmail.com>
 */
class OffsetLinkPager extends Widget
{
    /**
     * @var OffsetPagination the pagination object that this pager is associated with.
     * You must set this property in order to make OffsetLinkPager work.
     */
    public $pagination;

    /**
     * @var array HTML attributes for the pager container tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     * It also supports additional attribute tag, which identifies which tag to use for widget wrapper.
     * To disable this tag wrapper specify it as false.
     */
    public $options = ['class' => 'pagination'];

    /**
     * @var string link label.
     */
    public $linkLabel;

    /**
     * @var array HTML attributes for the link in a pager container tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $linkOptions = [];

    /**
     * @var bool whether to register link tags in the HTML header for prev, next, first and last page.
     * Defaults to `false` to avoid conflicts when multiple pagers are used on one page.
     * @see http://www.w3.org/TR/html401/struct/links.html#h-12.1.2
     * @see registerLinkTags()
     */
    public $registerLinkTags = true;

    /**
     * @var bool Hide widget when only one page exist.
     */
    public $hideOnSinglePage = true;

    /**
     * @var bool whether to hide widget when next page is not available.
     * Note: when this property is true it will not apply any "disabled" stuff.
     */
    public $hideOnNoNext = false;

    /**
     * @var bool whether to apply "disabled" stuff when next page is not available.
     * @see disabledOptions, disabledLinkOptions, disabledLinkLabel
     */
    public $disableOnNoNext = true;

    /**
     * @var array HTML attributes for the pager container tag,
     * that will be merged with [[options]] only when next page is not available and [[disableOnNoNext]] is true.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     * Note: if you want to merge e.g. CSS classes from [[options]] property,
     * you should specify both [[options]] and [[disabledOptions]] class values as arrays.
     */
    public $disabledOptions = [];

    /**
     * @var array HTML attributes for the link in a pager container tag,
     * that will be merged with [[linkOptions]] only when next page is not available and [[disableOnNoNext]] is true.
     * And also supports HTML tag
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     * Note: if you want to merge e.g. CSS classes from [[linkOptions]] property,
     * you should specify both [[linkOptions]] and [[disabledLinkOptions]] class values as arrays.
     */
    public $disabledLinkOptions = [
        'tag' => 'span',
        'class' => ['disabled']
    ];

    /**
     * @var string link label that will be shown, when next page is not available and [[disableOnNoNext]] is true.
     */
    public $disabledLinkLabel;

    /**
     * Initializes the pager.
     *
     * @return void
     * @throws InvalidConfigException when pagination property not set.
     */
    public function init()
    {
        if ($this->pagination === null) {
            throw new InvalidConfigException('The "pagination" property must be set.');
        }
    }

    /**
     * Executes the widget.
     *
     * @return string
     */
    public function run()
    {
        if ($this->registerLinkTags) {
            $this->registerLinkTags();
        }

        return $this->renderPageButtons();
    }

    /**
     * Registers relational link tags in the html header for next page.
     * These links are generated using [[\yii\data\Pagination::getLinks()]].
     *
     * @see http://www.w3.org/TR/html401/struct/links.html#h-12.1.2
     */
    protected function registerLinkTags()
    {
        $view = $this->getView();
        foreach ($this->pagination->getLinks() as $rel => $href) {
            $view->registerLinkTag(['rel' => $rel, 'href' => $href], $rel);
        }
    }

    /**
     * Renders the page button.
     *
     * @return string the rendering result
     */
    protected function renderPageButtons()
    {
        $button = '';
        $page = $this->pagination->getPage() + 1;
        $pageCount = $this->pagination->getPageCount();
        $hasNext = $page < $pageCount;

        if (($pageCount < 2 && $this->hideOnSinglePage) || (!$hasNext && $this->hideOnNoNext)) {
            return $button;
        }

        if (!$hasNext && $this->disableOnNoNext) {
            $this->options = ArrayHelper::merge($this->options,$this->disabledOptions);
        }

        $tag = ArrayHelper::remove($this->options, 'tag', 'div');
        $button = $this->renderPageButton($page, $hasNext);

        return $tag ? Html::tag($tag, $button, $this->options) : $button;
    }

    /**
     * Render a page button.
     * You may override this method to customize the generation of page buttons.
     *
     * @param int $page the page number
     * @param bool $hasNext whether next page is available
     * @return string the rendering result
     */
    protected function renderPageButton($page, $hasNext)
    {
        if (!$hasNext && $this->disableOnNoNext) {
            $this->linkOptions = ArrayHelper::merge($this->linkOptions, $this->disabledLinkOptions);
            $this->linkLabel = $this->disabledLinkLabel ?: Yii::t('app', 'There is no more content to load');
        }
        $this->linkOptions['data-page'] = $page;
        $tag = ArrayHelper::remove($this->linkOptions, 'tag', 'a');

        if ($tag === 'a') {
            $this->linkOptions['href'] = $this->pagination->createUrl($page);
        }

        return Html::tag($tag, $this->linkLabel, $this->linkOptions);
    }
}
