<?php

namespace api\modules\seo\controllers;

use common\modules\seo\models\Robots;
use yii\helpers\Url;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class SeoController
 *
 * @package api\modules\seo\controllers
 */
class SeoController extends Controller
{

    public function actionSitemap()
    {
        return $this->asXml(['test' => 'test']);
    }
    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionRobots()
    {
        $robots = Robots::findOneOrFail(['id' => 1]);

        response()->format = Response::FORMAT_RAW;
        $headers = response()->headers;
        $headers->add('Content-Type', 'text/plain');
        $this->layout = false;

        $text = $robots->text;

        $text .= "\n\nSitemap: " . configurator()->get('app.front.domain') . Url::toRoute(['/seo/seo/sitemap']);

        return $this->renderContent($text);
    }
}
