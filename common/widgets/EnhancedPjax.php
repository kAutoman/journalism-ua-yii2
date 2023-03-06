<?php

namespace common\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use common\assets\EnhancedPjaxAsset;

class EnhancedPjax extends Widget
{
    /**
     * @var string zone replacement constants.
     */
    const ZONE_REPLACE = 'replace';
    const ZONE_APPEND = 'append';
    const ZONE_PREPEND = 'prepend';

    /**
     * @var array the HTML attributes for the widget container tag. The following special options are recognized:
     *
     * - `tag`: string, the tag name for the container. Defaults to `div`
     *   This option is available since version 2.0.7.
     *   See also [[\yii\helpers\Html::tag()]].
     *
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = [];

    /**
     * @var array pjax javascript event handlers array.
     * Here is the list of available events:
     * Event name:    | Arguments:
     * click          | options
     * beforeSend     | xhr, options
     * start          | xhr, options
     * send           | xhr, options
     * clicked        | options
     * beforeReplace  | contents, options
     * success        | data, status, xhr, options
     * timeout        | xhr, options
     * error          | xhr, textStatus, error, options
     * complete       | xhr, textStatus, options
     * end            | xhr, options
     * @see https://github.com/defunkt/jquery-pjax#events
     * @example ```php
     * 'beforeSend' => 'function (xhr, options) {
     *     $('#LoadingMSG').show();
     * }',
     * 'complete': function(xhr, textStatus, options){
     *     $('#LoadingMSG').hide();
     * }
     * ```
     */
    public $events = [];

    /**
     * @var array sometimes you need to cancel standard plugin scenario and
     * proceed different parts of server response in different way,
     * this property made specially for this purpose.
     * To do so, specify this property as follows:
     * - array keys are CSS selectors which will be available in server response
     * - array values are zone replacement constants.
     * @example ```php
     * [
     *     '#list-view-items' => EnhancedPjax::ZONE_APPEND,
     *     '#list-view-pager' => EnhancedPjax::ZONE_REPLACE,
     * ]
     * ```
     */
    public $zones = [];

    /**
     * @var string|false the jQuery selector of the links that should trigger pjax requests.
     * If not set, all links within the enclosed content of Pjax will trigger pjax requests.
     * If set to false, no code will be registered to handle links.
     * Note that if the response to the pjax request is a full page, a normal request will be sent again.
     */
    public $linkSelector;

    /**
     * @var string|false the jQuery selector of the forms whose submissions should trigger pjax requests.
     * If not set, all forms with `data-pjax` attribute within the enclosed content of Pjax will trigger pjax requests.
     * If set to false, no code will be registered to handle forms.
     * Note that if the response to the pjax request is a full page, a normal request will be sent again.
     */
    public $formSelector;

    /**
     * @var string The jQuery event that will trigger form handler. Defaults to "submit".
     * @since 2.0.9
     */
    public $submitEvent = 'submit';

    /**
     * @var bool whether to enable push state.
     */
    public $enablePushState = true;

    /**
     * @var bool whether to enable replace state.
     */
    public $enableReplaceState = false;

    /**
     * @var int pjax timeout setting (in milliseconds). This timeout is used when making AJAX requests.
     * Use a bigger number if your server is slow. If the server does not respond within the timeout,
     * a full page load will be triggered.
     */
    public $timeout = 1000;

    /**
     * @var bool|int how to scroll the page when pjax response is received. If false, no page scroll will be made.
     * Use a number if you want to scroll to a particular place.
     */
    public $scrollTo = false;

    /**
     * @var array additional options to be passed to the pjax JS plugin. Please refer to the
     * [pjax project page](https://github.com/yiisoft/jquery-pjax) for available options.
     */
    public $clientOptions;

    /**
     * @inheritdoc
     * @internal
     */
    public static $counter = 0;

    /**
     * @inheritdoc
     */
    public static $autoIdPrefix = 'p';

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }

        if ($this->requiresPjax()) {
            ob_start();
            ob_implicit_flush(false);
            $view = $this->getView();
            $view->clear();
            $view->beginPage();
            $view->head();
            $view->beginBody();

            if ($view->title !== null) {
                echo Html::tag('title', Html::encode($view->title));
            }
        } else {
            $options = $this->options;
            $tag = ArrayHelper::remove($options, 'tag', 'div');
            echo Html::beginTag($tag, array_merge([
                'data-pjax-container' => '',
                'data-pjax-push-state' => $this->enablePushState,
                'data-pjax-replace-state' => $this->enableReplaceState,
                'data-pjax-timeout' => $this->timeout,
                'data-pjax-scrollto' => $this->scrollTo,
            ], $options));
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (!$this->requiresPjax()) {
            echo Html::endTag(ArrayHelper::remove($this->options, 'tag', 'div'));
            $this->registerClientScript();

            return;
        }

        $view = $this->getView();
        $view->endBody();

        // Do not re-send css files as it may override the css files that were loaded after them.
        // This is a temporary fix for https://github.com/yiisoft/yii2/issues/2310
        // It should be removed once pjax supports loading only missing css files
        $view->cssFiles = null;

        $view->endPage(true);

        $content = ob_get_clean();

        // only need the content enclosed within this widget
        $response = Yii::$app->getResponse();
        $response->clearOutputBuffers();
        $response->setStatusCode(200);
        $response->format = Response::FORMAT_HTML;
        $response->content = $content;
        $response->send();

        Yii::$app->end();
    }

    /**
     * @return bool whether the current request requires pjax response from this widget
     */
    protected function requiresPjax()
    {
        $headers = Yii::$app->getRequest()->getHeaders();

        return $headers->get('X-Pjax') && explode(' ', $headers->get('X-Pjax-Container'))[0] === '#' . $this->options['id'];
    }

    /**
     * Registers the needed JavaScript.
     */
    public function registerClientScript()
    {
        $view = $this->getView();
        $id = $this->options['id'];
        $this->clientOptions['push'] = $this->enablePushState;
        $this->clientOptions['replace'] = $this->enableReplaceState;
        $this->clientOptions['timeout'] = $this->timeout;
        $this->clientOptions['scrollTo'] = $this->scrollTo;
        $this->clientOptions['zones'] = $this->zones;
        if (!isset($this->clientOptions['container'])) {
            $this->clientOptions['container'] = "#$id";
        }
        $options = Json::htmlEncode($this->clientOptions);
        $js = '';
        if ($this->linkSelector !== false) {
            $linkSelector = Json::htmlEncode($this->linkSelector !== null ? $this->linkSelector : '#' . $id . ' a');
            $js .= "jQuery(document).pjax($linkSelector, $options);";
        }
        if ($this->formSelector !== false) {
            $formSelector = Json::htmlEncode($this->formSelector !== null ? $this->formSelector : '#' . $id . ' form[data-pjax]');
            $submitEvent = Json::htmlEncode($this->submitEvent);
            $js .= "\njQuery(document).on($submitEvent, $formSelector, function (event) {jQuery.pjax.submit(event, $options);});";
        }
        if (is_array($this->events) && !empty($this->events)) {
            foreach ($this->events as $event => $handler) {
                $js .= "\njQuery(document).on(\"pjax:$event\", $handler);";
            }
        }

        EnhancedPjaxAsset::register($view);

        if ($js !== '') {
            $view->registerJs($js);
        }
    }
}
