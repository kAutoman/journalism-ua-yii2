<?php

namespace backend\modules\faq\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Url;

/**
 * Class InfoBox
 *
 * @var string $bgColor
 * @var string $iconClass
 * @var string $title
 * @var string $number
 * @var string $subContent
 *
 * @package backend\widgets
 */
class RequestsInfoBox extends Widget
{
    /**
     * @var string
     */
    public $bgColor = 'aqua';
    /**
     * @var string
     */
    public $iconClass = 'fa fa-comment';
    /**
     * @var int
     */
    public $countQuery;

    /**
     * @return string
     */
    public function run()
    {
        $title = bt('Ask Question requests', 'faq-ask-question');

        return $this->render('info', [
            'countQuery' => $this->countQuery,
            'title' => $title,
            'bgColor' => $this->bgColor,
            'iconClass' => $this->iconClass,
            'link' => Url::toRoute(['/faq/faq-ask-question/index']),
        ]);
    }
}
