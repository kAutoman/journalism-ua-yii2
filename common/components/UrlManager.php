<?php

namespace common\components;

use yii\helpers\Url;
use yii\web\Application;
use yii\base\InvalidConfigException;
use yii\web\UrlManager as BaseUrlManager;
use common\helpers\LanguageHelper;

/**
 * Class UrlManager
 *
 * @package common\components
 */
class UrlManager extends BaseUrlManager
{
    /**
     * @var string
     */
    public $langParam = '_lang';
    /**
     * @var bool
     */
    public $showScriptName = false;
    /**
     * @var bool
     */
    public $enablePrettyUrl = true;
    /**
     * @var string
     */
    public $rulesPath = '@common/config/routes';
    /**
     * @var array
     */
    public $excludeFromClientRoutes = ['pages', 'home'];
    /**
     * @var bool
     */
    public $useDefaultLanguageCode = false;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        if (app() instanceof Application) {
            $rulesPath = getAlias($this->rulesPath) . DIRECTORY_SEPARATOR . YII_APP . '.php';
            $this->rules = require $rulesPath;

            $this->enableStrictParsing = YII_APP === 'api';
        }

        return parent::init();
    }

    /**
     * @inheritdoc
     */
    public function parseRequest($request)
    {
        $parent = parent::parseRequest($request);
        if (app() instanceof Application && YII_APP === 'api') {
            $acceptLang = request()->getHeaders()->get('Accept-Language');
            app()->language = in_array($acceptLang, LanguageHelper::getApplicationLanguages()) // choose default lang for direct browser requests
                ? request()->getHeaders()->get('Accept-Language')
                : LanguageHelper::getDefaultLanguage()->code;
        }

        return $parent;
    }

    /**
     * @param array|string $params
     * @return string
     */
    public function createUrl($params)
    {
        if (YII_APP === 'api') {
            if (isset($params[$this->langParam])) {
                $langPrefix = $params[$this->langParam];
                unset($params[$this->langParam]);
                $url = parent::createUrl($params);
                $parsedUrl = array_diff(explode('/', $url), $this->excludeFromClientRoutes);
                $url = implode('/', $parsedUrl);
                if ($this->useDefaultLanguageCode || $langPrefix !== LanguageHelper::getDefaultLanguage()->code) {
                    return '/' . $langPrefix . $url;
                }
                return !empty($url) ? $url : '/';
            }
            $url = parent::createUrl($params);
            $parsedUrl = array_diff(explode('/', $url), $this->excludeFromClientRoutes);
            $url = implode('/', $parsedUrl);
            return !empty($url) ? $url : '/';
        }
        return parent::createUrl($params);
    }

    /**
     * @param array|string $params
     * @param null $scheme
     * @return string
     */
    public function createAbsoluteUrl($params, $scheme = null)
    {
        $params = (array) $params;
        $url = $this->createUrl($params);
        if (strpos($url, '://') === false) {
            $hostInfo = configurator()->get('app.front.domain'); // change HostInfo
            $parsedUrl = array_diff(explode('/', $url), $this->excludeFromClientRoutes);
            $url = implode('/', $parsedUrl);
            if (strncmp($url, '//', 2) === 0) {
                $url = substr($hostInfo, 0, strpos($hostInfo, '://')) . ':' . $url;
            } else {
                $url = $hostInfo . $url;
            }
        }

        return Url::ensureScheme($url, $scheme);
    }
}
