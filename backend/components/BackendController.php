<?php

namespace backend\components;

use backend\actions\ActionAjaxValidation;
use backend\actions\ActionCreate;
use backend\actions\ActionDelete;
use backend\actions\ActionExport;
use backend\actions\ActionIndex;
use backend\actions\ActionUpdate;
use backend\actions\ActionView;
use common\helpers\LanguageHelper;
use common\models\User;
use vova07\imperavi\actions\GetImagesAction;
use vova07\imperavi\actions\UploadFileAction;
use Yii;
use yii\base\NotSupportedException;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\web\Controller;

/**
 * Class BackendController
 *
 * @property bool $canCreate
 * @property bool $canUpdate
 * @property bool $canDelete
 * @property bool $canExport
 *
 * @package backend\components
 */
abstract class BackendController extends Controller
{
    /**
     * @var bool
     */
    public $canCreate = true;

    /**
     * @var bool
     */
    public $canUpdate = true;

    /**
     * @var bool
     */
    public $canDelete = true;

    /**
     * @var bool
     */
    public $canExport = false;

    /**
     * @var string
     */
    public $defaultAction = 'index';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [User::ROLE_ADMIN],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @return string
     * @throws NotSupportedException
     */
    abstract public function getModelClass(): string;

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => ActionIndex::class,
                'modelClass' => $this->getModelClass(),
            ],
            'update' => [
                'class' => ActionUpdate::class,
                'modelClass' => $this->getModelClass(),
            ],
            'create' => [
                'class' => ActionCreate::class,
                'modelClass' => $this->getModelClass(),
            ],
            'ajax-validation' => [
                'class' => ActionAjaxValidation::class,
                'modelClass' => $this->getModelClass()
            ],
            'view' => [
                'class' => ActionView::class,
                'modelClass' => $this->getModelClass()
            ],
            'delete' => [
                'class' => ActionDelete::class,
                'modelClass' => $this->getModelClass(),
                'redirect' => ['index']
            ],
            'export' => [
                'class' => ActionExport::class,
                'modelClass' => $this->getModelClass(),
                'redirect' => ['index']
            ],
            'images-get' => [
                'class' => GetImagesAction::class,
                'url' => '/uploads/redactor/', // Directory URL address, where files are stored.
                'path' => '@webroot/uploads/redactor',
                'options' => [
                    'basePath' => Yii::getAlias('@webroot/uploads/redactor'),
                    'except' => ['.gitkeep']
                ]
            ],
            'image-upload' => [
                'class' => UploadFileAction::class,
                'url' => '/uploads/redactor/', // Directory URL address, where files are stored.
                'path' => 'uploads/redactor' // Absolute path to directory where files are stored.
            ],
        ];
    }


    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

}
