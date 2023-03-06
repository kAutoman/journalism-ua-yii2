<?php

use yii\web\User;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use common\helpers\Optional;
use common\helpers\HtmlDumper;
use common\helpers\HigherOrderTapProxy;

if (!function_exists('app')) {
    /** @return yii\console\Application|yii\web\Application current application instance. */
    function app()
    {
        return Yii::$app;
    }
}

if (!function_exists('view')) {
    /** @return yii\base\View|yii\web\View current application View component. */
    function view()
    {
        return app()->getView();
    }
}

if (!function_exists('request')) {
    /** @return yii\console\Request|\yii\web\Request|\common\components\Request */
    function request()
    {
        return app()->getRequest();
    }
}

if (!function_exists('urlManager')) {
    /** @return \yii\web\UrlManager|\common\components\UrlManager */
    function urlManager()
    {
        return app()->getUrlManager();
    }
}

if (!function_exists('response')) {
    /** @return yii\console\Response|yii\web\Response */
    function response()
    {
        return app()->getResponse();
    }
}

if (!function_exists('session')) {
    /** @return yii\web\Session */
    function session()
    {
        return app()->getSession();
    }
}

if (!function_exists('security')) {
    /** @return \yii\base\Security */
    function security()
    {
        return app()->getSecurity();
    }
}

if (!function_exists('db')) {
    /** @return \yii\db\Connection */
    function db()
    {
        return app()->getDb();
    }
}

if (!function_exists('formatter')) {
    /** @return \common\components\Formatter */
    function formatter()
    {
        return app()->getFormatter();
    }
}

if (!function_exists('configurator')) {
    /** @return common\modules\config\application\components\Configurator configurator component instance. */
    function configurator()
    {
        return app()->get('configurator');
    }
}

if (!function_exists('container')) {
    /** @return yii\di\Container DI container instance. */
    function container()
    {
        return Yii::$container;
    }
}

if (!function_exists('d')) {
    /**
     * Dump the passed variables and end the script execution.
     * @param mixed
     */
    function d(...$args)
    {
        foreach ($args as $value) {
            dumpValue($value, false);
        }
        die;
    }
}

if (!function_exists('dump')) {
    /**
     * Dump the passed variables.
     * @param mixed
     */
    function dump(...$args)
    {
        foreach ($args as $value) {
            dumpValue($value, false);
        }
    }
}

if (!function_exists('dumpValue')) {
    /**
     * Dump a value with elegance.
     * @param mixed $value
     * @param bool $exit
     */
    function dumpValue($value, $exit = false)
    {
        $dumper = in_array(PHP_SAPI, ['cli', 'phpdbg']) ? new CliDumper : new HtmlDumper;
        $dumper->dump((new VarCloner)->cloneVar($value));
        !$exit ?: die;
    }
}

if (!function_exists('optional')) {
    /**
     * Provide access to optional objects.
     * @param mixed $value
     * @return mixed
     */
    function optional($value)
    {
        return new Optional($value);
    }
}

if (!function_exists('t')) {
    /**
     * @param $message
     * @param string $category
     * @param array $params
     * @param null|string $language
     * @return string
     */
    function t($message, $category = 'app', $params = [], $language = null)
    {
        $prefix = 'front/';

        return yiit($message, $prefix . $category, $params, $language);
    }
}

if (!function_exists('bt')) {
    /**
     * @param $message
     * @param string $category
     * @param array $params
     * @param null|string $language
     * @return string
     */
    function bt($message, $category = 'app', $params = [], $language = null)
    {
        $prefix = 'back/';

        return yiit($message, $prefix . $category, $params, $language);
    }
}

if (!function_exists('yiit')) {
    /**
     * @param $message
     * @param string $category
     * @param array $params
     * @param null|string $language
     * @return string
     */
    function yiit($message, $category, $params = [], $language = null)
    {
        return Yii::t($category, $message, $params, $language);
    }
}

if (!function_exists('encode')) {
    /**
     * @param $content
     * @param bool $doubleEncode
     * @return string
     */
    function encode($content, $doubleEncode = true)
    {
        return Html::encode($content, $doubleEncode);
    }
}

if (!function_exists('alias')) {
    /**
     * @param string $alias
     * @param bool $throwException
     * @return bool|string
     */
    function alias($alias, $throwException = true)
    {
        return Yii::getAlias($alias, $throwException);
    }
}

if (!function_exists('a')) {
    /**
     * @param $text
     * @param null $url
     * @param array $options
     * @return string
     */
    function a($text, $url = null, $options = [])
    {
        return Html::a($text, $url, $options);
    }
}

if (!function_exists('text')) {
    /**
     * @param $content
     * @return string
     */
    function text($content)
    {
        return \nl2br(encode($content));
    }
}

if (!function_exists('asUrl')) {
    /**
     * @param $content
     * @return string
     */
    function asUrl($content)
    {
        return Yii::$app->formatter->asUrl($content);
    }
}

if (!function_exists('base64url_encode')) {
    /**
     * @param $data
     * @return string
     */
    function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}

if (!function_exists('base64url_decode')) {
    /**
     * @param $data
     * @return string
     */
    function base64url_decode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}

if (!function_exists('executeJs')) {
    /**
     * Register flash with js in session
     * @param $js
     */
    function executeJs($js)
    {
        session()->setFlash('executeJs', new JsExpression($js));
    }
}

if (!function_exists('merge')) {
    /**
     * Merges two or more arrays into one, recursively.
     * @param array ...$arrays arrays to merge.
     * @return array
     * @see ArrayHelper::merge()
     */
    function merge(array ...$arrays)
    {
        return ArrayHelper::merge(...$arrays);
    }
}

if (!function_exists('keyExists')) {
    /**
     * Checks if the given array contains the specified key.
     * This method enhances the `array_key_exists()` function by supporting case-insensitive
     * key comparison.
     * @param string $key the key to check
     * @param array $array the array with keys to check
     * @param bool $caseSensitive whether the key comparison should be case-sensitive
     * @return bool whether the array contains the specified key
     */
    function keyExists($key, $array, $caseSensitive = true)
    {
        return ArrayHelper::keyExists($key, $array, $caseSensitive);
    }
}

if (!function_exists('obtain')) {
    /**
     * @param string|Closure|array $key
     * @param array|object $from
     * @param null $default
     * @return mixed
     * @see ArrayHelper::getValue()
     */
    function obtain($key, $from, $default = null)
    {
        return ArrayHelper::getValue($from, $key, $default);
    }
}

if (!function_exists('map')) {
    /**
     * @param array $array
     * @param string|\Closure $from
     * @param string|\Closure $to
     * @param string|\Closure $group
     * @return array
     * @see ArrayHelper::map()
     */
    function map($array, $from, $to, $group = null)
    {
        return ArrayHelper::map($array, $from, $to, $group);
    }
}

if (!function_exists('getColumn')) {
    /**
     * @param array $array
     * @param string|\Closure $name
     * @param bool $keepKeys whether to maintain the array keys.
     * If false, the resulting array will be re-indexed with integers.
     * @return array the list of column values
     * @see ArrayHelper::getColumn()
     */
    function getColumn($array, $name, $keepKeys = true)
    {
        return ArrayHelper::getColumn($array, $name, $keepKeys);
    }
}

if (!function_exists('remove')) {
    /**
     * @param array $array the array to extract value from.
     * @param string $key key name of the array element.
     * @param mixed $default the default value to be returned if the specified key does not exist.
     * @return mixed|null the value of the element if found, default value otherwise.
     * @see ArrayHelper::remove()
     */
    function remove(&$array, $key, $default = null)
    {
        return ArrayHelper::remove($array, $key, $default);
    }
}

if (!function_exists('createObject')) {
    /** {@inheritdoc} */
    function createObject($type, array $params = [])
    {
        return Yii::createObject($type, $params);
    }
}

if (!function_exists('setAlias')) {
    /**
     * @param $alias
     * @param $path
     * @see Yii::setAlias()
     */
    function setAlias($alias, $path)
    {
        Yii::setAlias($alias, $path);
    }
}

if (!function_exists('getAlias')) {
    /**
     * @param $alias
     * @param bool $throwException
     * @see Yii::getAlias()
     * @return bool|string
     */
    function getAlias($alias, $throwException = true)
    {
        return Yii::getAlias($alias, $throwException);
    }
}

if (!function_exists('logError')) {
    /**
     * @param string|array $message
     * @param string $category
     */
    function logError($message, $category = 'application')
    {
        Yii::error($message, $category);
    }
}

if (!function_exists('logWarn')) {
    /**
     * @param string|array $message
     * @param string $category
     */
    function logWarn($message, $category = 'application')
    {
        Yii::warning($message, $category);
    }
}

if (!function_exists('logInfo')) {
    /**
     * @param string|array $message
     * @param string $category
     */
    function logInfo($message, $category = 'application')
    {
        Yii::info($message, $category);
    }
}

if (!function_exists('jsdump')) {
    /**
     * @param mixed $value
     * @return string
     */
    function jsdump($value)
    {
        header('Content-type: application/json; charset=utf-8');
        echo Json::encode(
            $value,
            JSON_OBJECT_AS_ARRAY |
            JSON_NUMERIC_CHECK |
            JSON_UNESCAPED_SLASHES |
            JSON_UNESCAPED_UNICODE |
            JSON_PRESERVE_ZERO_FRACTION
        );
        die;
    }
}

if (!function_exists('beginProfile')) {
    /** @inheritdoc */
    function beginProfile($token, $category = 'application')
    {
        Yii::beginProfile($token, $category);
    }
}

if (!function_exists('endProfile')) {
    /** @inheritdoc */
    function endProfile($token, $category = 'application')
    {
        Yii::endProfile($token, $category);
    }
}

if (!function_exists('tap')) {
    /**
     * Call the given Closure with the given value then return the value.
     * @param mixed $value
     * @param callable|null $callback
     * @return mixed
     * @link https://goo.gl/jCEi1Z
     */
    function tap($value, $callback = null)
    {
        if ($callback === null) {
            return new HigherOrderTapProxy($value);
        }
        $callback($value);

        return $value;
    }
}

if (!function_exists('user')) {
    /**
     * @return User
     */
    function user()
    {
        return app()->getUser();
    }
}

if (!function_exists('identity')) {
    /**
     * @return IdentityInterface
     * @throws Throwable
     */
    function identity()
    {
        return user()->getIdentity();
    }
}

if (!function_exists('isGuest')) {
    /**
     * @return bool
     */
    function isGuest()
    {
        return app()->getUser()->getIsGuest();
    }
}
