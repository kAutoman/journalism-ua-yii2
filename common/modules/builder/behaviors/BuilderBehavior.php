<?php

namespace common\modules\builder\behaviors;

use Closure;
use Yii;
use yii\base\{Behavior, InvalidConfigException, Model};
use yii\db\Exception;
use yii\helpers\{ArrayHelper, Json};
use common\components\model\ActiveRecord;
use common\helpers\LanguageHelper;
use common\modules\builder\models\{Builder, BuilderAttribute, BuilderModel};
use common\modules\builder\blocks\{Blockquote,
    ButtonLink,
    Editor,
    Heading,
    HeadingEditor,
    HeadingTextArea,
    HeadingTextAreaButton,
    HeadingTwoTextArea,
    Images,
    LeftRightTextArea,
    Video};

/**
 * Class BuilderBehavior
 *
 * Connection example:
 * ```
 * 'builder' => [
 *     'class' => BuilderBehavior::class,
 *     'attribute' => 'builderContent',
 *     'models' => [
 *         SampleModel::class
 *     ]
 * ]
 * ```
 *
 * Make sure `attribute` property is set correctly and has `safe` validation rules
 *
 * @package common\behaviors
 */
class BuilderBehavior extends Behavior
{
    const MODE_TYPE_DYNAMIC = 0;
    const MODE_TYPE_STATIC = 1;

    /** @var string Backend app identification */
    protected const BACKEND_APP_ID = 'app-backend';

    /**
     * @var ActiveRecord
     */
    public $owner;

    /**
     * Builder attribute, stores builder data array
     *
     * @var string
     */
    public $attribute;

    /**
     * Builder mode.
     * `Static` mode required predefined blocks without adding/sorting/remove features
     * `Dynamic` mode gives users full control on entity content
     *
     * @var int|Closure
     */
    public $mode = self::MODE_TYPE_DYNAMIC;

    /**
     * List of builder models connected to current model
     *
     * @var array
     */
    public $models = [];

    /**
     * Enable/disable blocks sorting
     * @var bool
     */
    public $isSortable = true;

    /**
     * Enable/disable blocks remove
     * @var bool
     */
    public $isRemovable = true;

    /**
     * Current language locale
     *
     * @var string
     */
    protected $language;

    /**
     * Default blocks setting.
     *
     * @var array
     */
    public static $defaultBlocks = [
        Heading::class,
        HeadingEditor::class,
        Editor::class,
        HeadingTextArea::class,
        HeadingTwoTextArea::class,
        HeadingTextAreaButton::class,
        LeftRightTextArea::class,
        Blockquote::class,
        ButtonLink::class,
        Images::class,
        Video::class,
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (!$this->attribute) {
            throw new InvalidConfigException('Property `attribute` not set!');
        }
        if (empty($this->models)) {
            throw new InvalidConfigException('`Models` array cannot be empty');
        }

        $this->language = Yii::$app->id === self::BACKEND_APP_ID
            ? LanguageHelper::getEditLanguage()
            : Yii::$app->language;

        $this->models = ArrayHelper::merge([bt('Default', 'builder') => self::$defaultBlocks], $this->models);

        return parent::init();
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
        ];
    }

    /**
     * Find and assign builder content to current model
     *
     * @throws InvalidConfigException
     */
    public function afterFind()
    {
        /** @var Builder[] $builderModels */
        $builderModels = Builder::find()
            ->andWhere([
                'target_attribute' => $this->attribute,
                'target_class' => $this->owner->formName(),
                'target_id' => $this->owner->id,
                'language' => $this->language,
            ])
            ->joinWith(['builderAttributes ab'])
            ->orderBy(['position' => SORT_ASC])
            ->all();


        $values = [];
        foreach ($builderModels as $builderModel) {

            $className = $builderModel->builder_model_class;
            $position = $builderModel->position;

            /** @var BuilderModel $builderClass */
            $builderClass = new $className();

            $builderClass->setIsNewRecord($builderModel->id === null);
            $builderClass->id = $builderModel->id;
            $builderClass->target_sign = $builderModel->target_sign;
            $builderClass->language = $this->language;
            $builderClass->tag_level = $builderModel->tag_level;
            $builderClass->component_name = $builderModel->component_name;
            $builderClass->published = $builderModel->published;

            $this->setMode($builderClass);

            foreach ($builderModel->builderAttributes as $builderAttribute) {
                if (in_array($builderAttribute->attribute, array_keys($builderClass->getUploadAttributes()))) {
                    $builderClass->{$builderAttribute->attribute} = $this->setUploadAttribute($builderAttribute);
                } else {
                    if ($builderClass->hasProperty($builderAttribute->attribute)) {
                        $builderClass->{$builderAttribute->attribute} = in_array($builderAttribute->attribute,
                            $builderClass->getArrayAttributes()) ? Json::decode($builderAttribute->value,
                            true) : $builderAttribute->value;
                    }
                }
            }
            $values[$position] = $builderClass;
        }

        $this->owner->{$this->attribute} = $values;
    }

    /**
     * Trigger builder moder validation on server side.
     *
     * @throws InvalidConfigException
     * @todo make client-side validation
     */
    public function beforeValidate()
    {
        $builderModels = [];
        $blocks = array_merge(...(array_values($this->models)));

        foreach ($blocks as $className) {
            $models = [];
            /** @var BuilderModel $class */
            $class = new $className();
            $postParams = \Yii::$app->getRequest()->post($class->formName(), []);

            foreach ($postParams as $key => $values) {
                $models[] = $this->proceedParams($class, $values, $key);
            }

            Model::validateMultiple($models);
            $levels = array_count_values(array_column($models, 'tag_level'));

            /** @var BuilderModel[] $models */
            foreach ($models as $model) {
                $errors = $model->getErrors();
                if (isset($levels[1]) && $levels[1] > 1) {
                    $this->owner->addError($this->attribute, bt('Cannot be more then one H1 tag level.', 'builder'));
                }
                foreach ($errors as $attribute => $attributeErrors) {
                    $attribute = $model->formName() . "[{$model->position}][{$attribute}]";
                    foreach ($attributeErrors as $error) {
                        $this->owner->addError($attribute, $error);
                    }
                }
                $builderModels[$model->position] = $model;
            }
            $this->owner->{$this->attribute} = $builderModels;
        }
        // implement correct sorting after validation
        ksort($this->owner->{$this->attribute});
    }

    /**
     * Triggers builder model saving. If the owner record is not new - resets already existing data.
     *
     * @return void
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function afterSave()
    {
        $blocks = array_merge(...(array_values($this->models)));
        foreach ($blocks as $className) {
            /** @var BuilderModel $class */
            $class = new $className();
            $class->loadDefaultValues();
            $postParams = Yii::$app->getRequest()->post($class->formName(), []);

            foreach ($postParams as $key => $values) {
                $models[] = $this->proceedParams($class, $values, $key)->saveRecord($this->owner);
            }
        }
    }

    public function getFormattedModels(): array
    {
        $models = [];
        foreach ($this->models as $group => $blocks) {
            foreach ($blocks as $block) {
                $models[$group][$block] = $block::getTitle();
            }
        }

        return $models;
    }


    /**
     * Set post params after form submit to current builder model
     *
     * @param BuilderModel $class
     * @param array $values
     * @param int $key
     *
     * @return BuilderModel
     */
    protected function proceedParams(BuilderModel $class, array $values, int $key): BuilderModel
    {
        /** @var BuilderModel $builderModel */
        $builderModel = new $class();

        if (isset($values['id']) && !!$values['id'] && Builder::findOne($values['id'])) {
            $builderModel->id = $values['id'];
        }
        $builderModel->loadDefaultValues();
        $builderModel->setAttributes($values);

        $this->setMode($builderModel);

        $builderModel->position = $key;
        $builderModel->target_attribute = $this->attribute;
        $builderModel->language = LanguageHelper::getEditLanguage();
        $builderModel->tag_level = $values['tag_level'] ?? BuilderModel::DEFAULT_TAG_LEVEL;
        $builderModel->component_name = $values['component_name'] ?? null;
        $builderModel->published = $values['published'] ?? true;
        if (isset($values['target_sign'])) {
            $builderModel->target_sign = $values['target_sign'];
        }

        return $builderModel;
    }

    /**
     * Assign file uploads entities id to model upload attributes.
     *
     * For single entity files - attribute value will be string.
     *
     * As for ImageUpload can't accept array as value,
     * for multiple uploads - JSON string will be returned.
     * You should make `json_decode()` this value to get the array.
     *
     * @param BuilderAttribute $attribute
     *
     * @return string|array
     */
    protected function setUploadAttribute(BuilderAttribute $attribute)
    {
        $attributes = json_decode($attribute->value);
        if ($attributes !== null && count($attributes) === 1 && isset($attributes[0])) {
            return $attributes[0];
        }

        return $attribute->value;
    }

    /**
     * @param BuilderModel $builderModel
     */
    public function setMode(BuilderModel $builderModel)
    {
        if ($this->mode instanceof Closure) {
            $this->mode = call_user_func($this->mode, $this->owner);
        }
        if ($this->mode === self::MODE_TYPE_STATIC) {
            $builderModel->setIsSortable(false);
            $builderModel->setIsRemovable(false);
        } else {
            $builderModel->setIsSortable($this->isSortable);
            $builderModel->setIsRemovable($this->isRemovable);
        }
    }

    /**
     * @return Closure|int|mixed
     */
    public function getMode()
    {
        if ($this->mode instanceof Closure) {
            $this->mode = call_user_func($this->mode, $this->owner);
        }

        return $this->mode;
    }
}
