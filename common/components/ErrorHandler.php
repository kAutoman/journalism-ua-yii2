<?php

namespace common\components;

use Yii;
use yii\web\Response;
use yii\base\Exception;
use yii\web\HttpException;
use yii\base\UserException;
use yii\base\ErrorException;
use yii\base\ErrorHandler as BaseErrorHandler;
use Whoops\Run as Whoops;
use Whoops\Handler\PrettyPageHandler;

/**
 * Whoops replacement for Yii2 Error Handler.
 *
 * @author Bogdan K. Fedun <delagics@gmail.com>
 */
class ErrorHandler extends BaseErrorHandler
{
    /**
     * @var string the route (e.g. `site/error`) to the controller action that will be used
     * to display external errors. Inside the action, it can retrieve the error information
     * using `app()->errorHandler->exception`. This property defaults to null, meaning ErrorHandler
     * will handle the error display.
     */
    public $errorAction;

    /**
     * Get the Whoops handler for the application.
     *
     * @return \Whoops\Handler\Handler
     */
    protected function whoopsHandler()
    {
        return tap(new PrettyPageHandler, function (PrettyPageHandler $handler) {
            $handler->handleUnconditionally(true);
            $handler->setApplicationPaths([
                getAlias('@common'),
                getAlias('@frontend'),
                getAlias('@backend'),
                getAlias('@console'),
                getAlias('@webroot'),
            ]);
        });
    }

    /**
     * Render an exception to a string using "Whoops".
     *
     * @param Exception $e
     * @return string
     */
    protected function renderExceptionWithWhoops($e)
    {
        return tap(new Whoops, function (Whoops $whoops) {
            $whoops->pushHandler($this->whoopsHandler());
            $whoops->writeToOutput(false);
            $whoops->allowQuit(false);
        })->handleException($e);
    }

    /**
     * Renders the exception.
     *
     * @param Exception $exception the exception to be rendered.
     * @throws \yii\base\InvalidRouteException
     * @throws \yii\console\Exception
     */
    public function renderException($exception)
    {
        if (app()->has('response')) {
            $response = app()->getResponse();
            $response->isSent = false;
            $response->stream = null;
            $response->data = null;
            $response->content = null;
        } else {
            $response = new Response();
        }
        $response->setStatusCodeByException($exception);
        $useErrorView = $response->format === Response::FORMAT_HTML && (!YII_DEBUG || $exception instanceof UserException);
        if ($useErrorView && $this->errorAction !== null) {
            $result = app()->runAction($this->errorAction);
            if ($result instanceof Response) {
                $response = $result;
            } else {
                $response->data = $result;
            }
        } elseif ($response->format === Response::FORMAT_HTML) {
            if ($this->shouldRenderSimpleHtml()) {
                $response->data = '<pre>' . $this->htmlEncode(static::convertExceptionToString($exception)) . '</pre>';
            } else {
                $response->data = $this->renderExceptionWithWhoops($exception);
            }
        } elseif ($response->format === Response::FORMAT_RAW) {
            $response->data = static::convertExceptionToString($exception);
        } else {
            $response->data = $this->convertExceptionToArray($exception);
        }

        $response->send();
    }

    /**
     * Converts special characters to HTML entities.
     *
     * @param string $text to encode.
     * @return string encoded original text.
     */
    public function htmlEncode($text)
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }

    /**
     * @return bool if simple HTML should be rendered
     * @since 2.0.12
     */
    protected function shouldRenderSimpleHtml()
    {
        return YII_ENV_TEST || isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    /**
     * Converts an exception into an array.
     *
     * @param \Exception|\Error $exception the exception being converted
     * @return array the array representation of the exception.
     */
    protected function convertExceptionToArray($exception)
    {
        if (!YII_DEBUG && !$exception instanceof UserException && !$exception instanceof HttpException) {
            $exception = new HttpException(500, Yii::t('yii', 'An internal server error occurred.'));
        }
        $array = [
            'name' => ($exception instanceof Exception || $exception instanceof ErrorException) ? $exception->getName() : 'Exception',
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
        ];
        if ($exception instanceof HttpException) {
            $array['status'] = $exception->statusCode;
        }
        if (YII_DEBUG) {
            $array['type'] = get_class($exception);
            if (!$exception instanceof UserException) {
                $array['file'] = $exception->getFile();
                $array['line'] = $exception->getLine();
                $array['stack-trace'] = explode("\n", $exception->getTraceAsString());
                if ($exception instanceof \yii\db\Exception) {
                    $array['error-info'] = $exception->errorInfo;
                }
            }
        }
        if (($prev = $exception->getPrevious()) !== null) {
            $array['previous'] = $this->convertExceptionToArray($prev);
        }

        return $array;
    }
}
