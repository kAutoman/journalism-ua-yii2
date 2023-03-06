<?php

namespace common\modules\config\application\controllers;

use common\helpers\MediaHelper;
use common\models\UserLog;
use Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Request;
use yii\helpers\Json;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\helpers\Inflector;
use yii\filters\AccessControl;
use metalguardian\fileProcessor\helpers\FPM;
use common\models\User;
use common\helpers\LanguageHelper;
use common\modules\config\application\ConfigModule;
use common\modules\config\application\components\DynamicModel;
use common\modules\config\infrastructure\entities\IConfigEntity;
use common\modules\config\infrastructure\aggregates\IConfigAggregate;
use common\modules\config\infrastructure\services\IConfigManagerService;
use common\modules\config\infrastructure\services\IConfigEntityFormRenderer;
use common\modules\config\application\exceptions\InvalidAggregateClassException;

/**
 * Class AggregateController responsible for handling updates of configuration based aggregates.
 *
 * @author Bogdan K. Fedun <delagics@gmail.com>
 */
class AggregateController extends \yii\web\Controller
{
    /** @var string controller language. */
    private $lang;
    /** @var IConfigManagerService */
    private $manager;
    /** @var string the name of aggregate in lowercase. */
    private $aggregateName;
    /** @var string layout. */
    public $layout = '@backend/views/layouts/main.php';
    /** @var bool CSRF validation is not supported for now. */
    public $enableCsrfValidation = false;

    /**
     * AdminController constructor.
     * @param string $id
     * @param ConfigModule $module
     * @param Request $request
     * @param array $config
     * @throws InvalidAggregateClassException
     * @todo simplify constructor, get rid of language setting in future releases.
     */
    public function __construct(string $id, ConfigModule $module, Request $request, $config = [])
    {
        $this->lang = LanguageHelper::getEditLanguage();
        /** @var IConfigAggregate $aggregate */
        $aggregate = $this->makeAggregateInstance($request, $module->aggregatedEntitiesNamespace);
        $this->manager = createObject(IConfigManagerService::class, [$aggregate->getSpecifications(), true]);
        parent::__construct($id, $module, $config);
    }

    /**
     * Index action, handles configuration form.
     * @return string
     * @todo simplify method.
     */
    public function actionIndex()
    {

        $collection = $this->manager->all($this->lang);
        $content_before = [];
        foreach ($collection as $key => $item) {
            $content_before[$key] = $item->getValue();
        }


        if (request()->getIsPost()) {
            $values = [];
            foreach (request()->post() as $key => $value) {
                $values[str_replace('_', '.', $key)] = $value;
            }
            $this->manager->setMultiple($values, $this->lang);
            session()->setFlash('success', [bt('Successfully saved')], false);
            $log = new UserLog();
            $log->action = UserLog::ACTION_UPDATE;
            $log->model_class = get_class($this);
            $log->entity_id = $this->aggregateName;
            $log->user_id = \Yii::$app->user->getId();
            $log->content_before = UserLog::encode($content_before);
            $log->content_after = UserLog::encode($values);
            $log->user_info = UserLog::encode($_SERVER);
            $log->save();

            return $this->refresh();
        }
        return  $this->renderIndex();
    }

    protected function renderIndex()
    {
        $collection = $this->manager->all($this->lang);
        $this->view->title = bt($this->aggregateName, 'config');
        $form = createObject([
            'class' => IConfigEntityFormRenderer::class,
            'validationUrl' => ["/config/{$this->aggregateName}/validate"],
        ], [$collection]);

        return $this->render('index', compact('form'));
    }

    protected function validate($values)
    {
        $errors = [];
        $collection = $this->manager->all($this->lang);
        $collection->each(function (IConfigEntity $entity) use ($values, &$errors) {
            $rules = $entity->getField()->getRules();
            $name = $entity->getField()->getName();
            $attributes = [$name => obtain($name, $values)];
            $labels = [$name => $entity->getField()->getLabel()];
            foreach ($rules as $key => &$rule) {
                array_unshift($rule, [$name]);
            }
            $model = tap(new DynamicModel($attributes, $rules, $labels))->validate();
            $entityErrors = $model->getErrors(); //obtain($name, $model->getErrors(), []);



            if (!empty($entityErrors)) {
                //$errors = merge($errors, [$entity->getField()->getInputId() => $entityErrors]);
                $errors = merge($errors, $entityErrors);
            }
        });

        return $errors;
    }

    /**
     * Action that validates entities via AJAX request.
     * @return array
     * @todo simplify method.
     */
    public function actionValidate()
    {
        if (request()->getIsAjax()) {
            response()->format = Response::FORMAT_JSON;

            $values = request()->post();
            return $this->validate($values);
        }
    }

    /**
     * Upload config entity file.
     * @param string $key config entity key.
     * @param string $lang config entity lang.
     * @param bool $multiple config entity lang.
     * @param bool $webp config webp compress.
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUploadFile(
        string $key,
        string $lang,
        bool $multiple = false,
        bool $webp = false,
        int $limit = 1
    ) {
        if ($multiple) {
            $items = explode(',', $this->manager->get($key, $this->lang)->getValue());
            $items = array_filter($items);
            if (count($items) >= $limit) {
                $output['error'][] = bt('Limit upload files - ') . ' ' . $limit;
                return Json::encode($output);
            }
        }
        $output = [];
        $key = str_replace('_', '.', $key);
        $entity = $this->manager->get($key, $lang);
        $files = UploadedFile::getInstancesByName($entity->getField()->getName());
        if ($files) {
            foreach ($files as $file) {
                $originalName = "{$file->baseName}.{$file->extension}";
                $fileId = FPM::transfer()->saveUploadedFile($file);
                if ($fileId) {
                    $newValue = [$fileId];
                    if ($multiple) {
                        $oldValue = empty($entity->getValue()) ? [] : explode(',', $entity->getValue());
                        $newValue = merge($oldValue, $newValue);
                    }
                    $this->manager->set($key, $lang, implode(',', $newValue));
                    $output['id'] = $fileId;
                    $output['url'] = FPM::originalSrc($fileId);
                    $output['deleteUrl'] = Url::to([
                        '/config/aggregate/delete-file',
                        'aggregate' => request()->get('aggregate'),
                        'key' => $key,
                        'lang' => $lang,
                        'id' => $fileId,
                        'limit' => $limit,
                    ]);
                    MediaHelper::optimize($fileId);
                } else {
                    $output['error'][] = bt('Cannot save file') . ' ' . $originalName;
                }
            }
        }

        return Json::encode($output);
    }

    /**
     * @param string $key
     * @param string $lang
     * @param int $id
     * @return string
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionDeleteFile(string $key, string $lang, int $id)
    {
        $data = [];
        MediaHelper::delete($id);
        $isDeleted = FPM::deleteFile($id);
        if ($isDeleted) {
            $key = str_replace('_', '.', $key);
            $entity = $this->manager->get($key, $lang);
            $values = explode(',', $entity->getValue());
            if (($index = array_search($id, $values)) !== false) {
                unset($values[$index]);
            }
            empty($values)
                ? $this->manager->forget($key, $lang)
                : $this->manager->set($key, $lang, implode(',', $values));
        } else {
            $data[] = ['error' => bt('Cannot delete file')];
        }

        return Json::encode($data);
    }

    /**
     * Make an aggregate instance.
     * @param Request $request
     * @param $baseNamespace
     * @return object
     * @throws InvalidAggregateClassException when aggregate couldn't instantiate.
     */
    private function makeAggregateInstance(Request $request, $baseNamespace)
    {
        $aggregateName = Inflector::id2camel($request->get('aggregate'));

        try {
            $this->aggregateName = $aggregateName;
            return createObject("$baseNamespace\\$aggregateName", [strtolower($aggregateName), $this->lang]);
        } catch (Exception $e) {
            $message = "Looks like you are trying to access '$aggregateName' config aggregate class. ";
            $message .= "But there is no such class in '{$baseNamespace}' namespace. ";
            $message .= 'Either create one or check the correctness of your URL.';
            throw new InvalidAggregateClassException($message, 0, $e);
        }
    }
}
