<?php

namespace common\helpers;

use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * Class UrlHelper
 *
 * @package common\helpers
 */
class UrlHelper extends Url
{
    /**
     * Creates URL for client and API requests.
     * @see \common\components\UrlManager::$langParam to create localized links.
     *
     * @param string $route Base route
     * @param array $params Passed params.
     * @param bool $scheme If passed `true`, absolute URL will be created.
     *
     * @return string
     */
    public static function createUrl(string $route, array $params = [], bool $scheme = false): string
    {
        return static::to(ArrayHelper::merge([$route], $params), $scheme);
    }

    public static function canonical()
    {
        $params = app()->controller->actionParams;
        $params[0] = app()->controller->getRoute();

        return static::getUrlManager()->createAbsoluteUrl($params);
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public static function getMainPageUrl(array $params = []): string
    {
        return static::createUrl('/page/page/index', $params);
    }
}
