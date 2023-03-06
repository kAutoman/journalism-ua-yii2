<?php

namespace api\components;

use Throwable;
use yii\filters\Cors;
use yii\web\Response;
use yii\rest\Controller;
use yii\web\IdentityInterface;
use yii\filters\ContentNegotiator;
use backend\modules\user\models\User;

/**
 * Class RestController
 *
 * @property boolean $requireAuth
 * @property IdentityInterface|User|null $user
 *
 * @package api\components
 */
class RestController extends Controller
{
    /**
     * Flag actions without optional authenticator
     *
     * @var bool
     */
    public $requireAuth = false;

    /**
     * @var null|User
     */
    private $user = null;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => [ // do not use wildcard for iOS <= 12 compatibility
                    'Accept',
                    'Content-Type',
                    'Access-Control-Allow-Headers',
                    'X-Requested-With',
                    'Authorization',
                ],
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Max-Age' => 86400,
                'Access-Control-Expose-Headers' => [],
            ]
        ];

        $behaviors['verbFilter']['actions'] = [
            '*' => ['GET', 'OPTIONS', 'HEAD', 'POST'],
        ];

        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => ['application/json' => Response::FORMAT_JSON]
        ];

        unset($behaviors['authenticator']);
        $behaviors['authenticator'] = [
            'class' => ApiHttpBearerAuth::class,
        ];
        if (!$this->requireAuth) {
            $behaviors['authenticator']['optional'] = ['*'];
        }

        return $behaviors;
    }

    /**
     * @return User|IdentityInterface|null
     * @throws Throwable
     */
    protected function getUser(): ?IdentityInterface
    {
        if ($this->user === null) {
            $this->user = identity();
        }
        return $this->user;
    }
}
