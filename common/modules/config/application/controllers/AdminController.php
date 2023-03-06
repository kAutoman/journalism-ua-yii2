<?php

namespace common\modules\config\application\controllers;

use common\helpers\MediaHelper;
use common\models\EntityToFile;
use common\models\File;
use common\models\UserLog;
use yii\helpers\Url;
use yii\helpers\Json;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use metalguardian\fileProcessor\helpers\FPM;
use common\models\User;
use common\helpers\LanguageHelper;
use common\modules\config\application\ConfigModule;
use common\modules\config\application\components\DynamicModel;
use common\modules\config\infrastructure\entities\IConfigEntity;
use common\modules\config\infrastructure\services\IConfigManagerService;
use common\modules\config\infrastructure\services\IConfigEntityFormRenderer;

/**
 * Class AdminController responsible for handling application configuration updates.
 *
 * @author Bogdan K. Fedun <delagics@gmail.com>
 */
class AdminController extends \yii\web\Controller
{
    /** @var string controller language. */
    private $lang;
    /** @var IConfigManagerService */
    private $manager;
    /** @var string layout. */
    public $layout = '@backend/views/layouts/main.php';
    /** @var bool CSRF validation is not supported for now. */
    public $enableCsrfValidation = false;

    /**
     * AdminController constructor.
     * @param string $id
     * @param ConfigModule $module
     * @param array $config
     * @todo make application configuration suitable for handling by aggregate controller.
     */
    public function __construct(string $id, ConfigModule $module, $config = [])
    {
        $this->lang = LanguageHelper::getEditLanguage();
        $this->manager = createObject(IConfigManagerService::class, [$module->getSpecifications(), false]);
        parent::__construct($id, $module, $config);
    }

    /**
     * Index action, handles configuration form.
     * @return string
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
            $log->entity_id = 'settings';
            $log->user_id = \Yii::$app->user->getId();
            $log->content_before = UserLog::encode($content_before);
            $log->content_after = UserLog::encode($values);
            $log->user_info = UserLog::encode($_SERVER);
            $log->save();

            return $this->refresh();
        }


        $form = createObject([
            'class' => IConfigEntityFormRenderer::class,
            'validationUrl' => ["/config/admin/validate"],
        ], [$collection]);

        return $this->render('index', compact('form'));
    }

    /**
     * Action that validates entities via AJAX request.
     * @return array
     */
    public function actionValidate()
    {
        if (request()->getIsAjax()) {
            response()->format = Response::FORMAT_JSON;
            $errors = [];
            $values = request()->post();
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
                $entityErrors = obtain($name, $model->getErrors(), []);
                if (!empty($entityErrors)) {
                    $errors = merge($errors, [$entity->getField()->getInputId() => $entityErrors]);
                }
            });

            return $errors;
        }
    }

    /**
     * Upload config entity file.
     * @param string $key config entity key.
     * @param string $lang config entity lang.
     * @param bool $multiple config entity lang.
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUploadFile(string $key, string $lang, bool $multiple = false)
    {
        $output = [];
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
                        '/config/admin/delete-file',
                        'key' => $key,
                        'lang' => $lang,
                        'id' => $fileId
                    ]);
                    MediaHelper::optimize($fileId);
                } else {
                    $output['error'][] = bt('Cannot save file') . ' ' . $originalName;
                }
            }
        }

        return Json::encode($output);
    }

    public function actionSortFiles()
    {
        $sortOrder = request()->post('sort');

        if ($sortOrder) {
            $sortOrder = explode(',', $sortOrder);
            $i = count($sortOrder);
            foreach ($sortOrder as $fileId) {
                \Yii::$app->db->createCommand()->update(
                    File::tableName(),
                    [
                        'position' => $i
                    ],
                    'id = :id',
                    [':id' => (int)$fileId]
                )->execute();

                $i--;
            }
        }

        return Json::encode([]);
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
}
