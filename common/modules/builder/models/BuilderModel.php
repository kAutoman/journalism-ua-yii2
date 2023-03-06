<?php

namespace common\modules\builder\models;

use Yii;
use yii\db\Exception;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use ReflectionClass;
use ReflectionException;
use common\models\EntityToFile;
use common\helpers\LanguageHelper;
use common\components\model\ActiveRecord;

/**
 * Class BuilderModel
 *
 * @property int $id
 * @property string $language
 * @property string $builder_model_class
 * @property string $target_class
 * @property int $target_id
 * @property string $target_sign
 * @property string $target_attribute
 * @property int $tag_level
 * @property string $component_name
 * @property int $position
 * @property int $created_at
 * @property int $updated_at
 * @property boolean $published
 *
 * @property array $tagLevelRange
 *
 * @package common\modules\builder\models
 */
abstract class BuilderModel extends ActiveRecord
{
    const DEFAULT_TAG_LEVEL = 2;

    /**
     * Block tag levels range.
     * Default range from 1 to 6 sets on `init()`
     * May be overwritten in children classes for any individual block
     *
     * @var array
     */
    public $tagLevelRange = [];

    /**
     * Enable/Disable sorting.
     * Value sets in {{BuilderBehavior}}
     *
     * @var boolean
     */
    private $isSortable;

    /**
     * Enable/Disable block remove.
     * Value sets in {{BuilderBehavior}}
     *
     * @var boolean
     */
    private $isRemovable;

    /**
     * Temporary store for uploaded files array
     *
     * @var array|null
     */
    private $uploads;

    /**
     * List of ALL attributes corresponding to $this model
     *
     * @return array
     */
    abstract public function getBuilderAttributes(): array;


    /**
     * Returns the validation rules for attributes.
     * The same as default {{rules()}} method.
     *
     * @return array
     */
    abstract public function validationRules(): array;

    /**
     * Title for current builder block
     *
     * @return string
     */
    abstract public static function getTitle(): string;

    /**
     * Array of properties labels
     *
     * @return array
     * @see `attributeLabels()` in {ActiveRecord}
     */
    abstract public function getAttributeLabels(): array;

    /**
     * Form data config. Passes to {FormBuilder} to generate form fields
     *
     * @return array
     * @see \common\modules\builder\widgets\DummyFormBuilder
     */
    abstract public function getFormConfig(): array;

    /**
     * Block attributes for API response.
     *
     * @return array
     */
    abstract public function getApiAttributes(): array;


    /**
     * @inheritdoc
     *
     * @throws \yii\base\Exception
     */
    public function init()
    {
        parent::init();
        // generate sign for file uploader
        if ($this->target_sign === null) {
            $this->target_sign = Yii::$app->getSecurity()->generateRandomString();
        }
        if (empty($this->tagLevelRange)) {
            $range = range(1, 6);
            $this->tagLevelRange = array_combine($range, $range);
        }
    }

    /**
     * @inheritdoc
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%builder}}';
    }

    /**
     * Default rules for main builder model.
     * For child classes, method MUST be merged with this rules
     *
     * @return array
     */
    public function rules()
    {
        $defaultRules = [
            [
                [
                    'builder_model_class',
                    'target_class',
                    'target_attribute',
                    'target_sign',
                    'language',
                    'component_name'
                ],
                'string'
            ],
            [['target_attribute', 'position', 'language'], 'required'],
            [['target_id', 'position', 'created_at', 'updated_at', 'tag_level'], 'integer'],
            [['tag_level'], 'in', 'range' => $this->tagLevelRange],
            [['tag_level'], 'default', 'value' => self::DEFAULT_TAG_LEVEL],
            [['position', 'target_id'], 'default', 'value' => 0],
            [['published'], 'boolean'],
            [['published'], 'default', 'value' => 1],
        ];

        return ArrayHelper::merge($defaultRules, $this->validationRules());
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    public function behaviors(): array
    {
        return ['timestamp' => TimestampBehavior::class];
    }

    /**
     * Classname or specific name to identify builder class in REST API response.
     *
     * @return string
     * @throws ReflectionException
     */
    public function getShortName(): string
    {
        if (!empty($this->component_name)) {
            return $this->component_name;
        }

        return (new ReflectionClass($this))->getShortName();
    }

    /**
     * Save current builder and related attributes models.
     * If saving was successful - current builder model id will be returned.
     * Otherwise - false.
     *
     * @param ActiveRecord $target
     *
     * @return bool|int
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function saveRecord(ActiveRecord $target)
    {
        $new = false;
        if (!!$this->id) {
            $new = true;
            $model = Builder::findOne($this->id);
            $model->position = $this->position;
            $model->tag_level = $this->tag_level;
            $model->component_name = $this->component_name;
            $model->published = $this->published;
            $model->save();
        }
        $this->builder_model_class = get_called_class();
        $this->target_class = $target->formName();
        $this->target_id = $target->getPrimaryKey();
        $this->language = LanguageHelper::getEditLanguage();
        if ($this->id || $this->save()) {
            $attributes = $this->getBuilderAttributes();
            $valid = true;

            //$this->updateImages();

            foreach ($attributes as $key => $attribute) {
                $attributeModel = BuilderAttribute::findOne([
                    'builder_id' => $this->id,
                    'attribute' => $attribute,
                ]);


                if ($attributeModel === null) {
                    $attributeModel = new BuilderAttribute();
                    $attributeModel->builder_id = $this->id;
                    $attributeModel->attribute = $attribute;
                }

                if (in_array($attribute, array_keys($this->getUploadAttributes()))) {
                    $attributeModel->value = $this->uploadedFiles($attribute, $new);
                } else {
                    $attributeModel->value = is_array($this->$attribute) ? Json::encode($this->$attribute) : $this->$attribute;
                }
                $attributeModel->save() && $valid;
            }
            if ($valid) {
                return $this->id;
            }
        }

        return false;
    }

    /**
     * Sets the attribute values in a massive way.
     *
     * @param array $values attribute values (name => value) to be assigned to the model.
     * @param bool $safeOnly whether the assignments should only be done to the safe attributes.
     * A safe attribute is one that is associated with a validation rule in the current [[scenario]].
     *
     * @see safeAttributes()
     * @see attributes()
     */
    public function setAttributes($values, $safeOnly = true)
    {
        if (is_array($values)) {
            $attributes = array_flip($safeOnly ? $this->safeAttributes() : $this->attributes());
            foreach ($values as $name => $value) {
                if (isset($attributes[$name])) {
                    $this->$name = $value;
                } elseif ($safeOnly) {
                    $this->onUnsafeAttribute($name, $value);
                }
            }
        }
    }

    /**
     * Returns attribute values.
     *
     * @param array $names list of attributes whose value needs to be returned.
     * Defaults to null, meaning all attributes listed in [[attributes()]] will be returned.
     * If it is an array, only the attributes in the array will be returned.
     * @param array $except list of attributes whose value should NOT be returned.
     *
     * @return array attribute values (name => value).
     */
    public function getAttributes($names = null, $except = [])
    {
        $values = [];
        if ($names === null) {
            $names = $this->attributes();
        }
        foreach ($names as $name) {
            $values[$name] = $this->$name;
        }
        foreach ($except as $name) {
            unset($values[$name]);
        }

        return $values;
    }

    /**
     * @return mixed
     */
    public function getIsSortable(): bool
    {
        return $this->isSortable;
    }

    /**
     * @param bool $isSortable
     */
    public function setIsSortable(bool $isSortable): void
    {
        $this->isSortable = $isSortable;
    }

    /**
     * @return mixed
     */
    public function getIsRemovable(): bool
    {
        return $this->isRemovable;
    }

    /**
     * @param bool $isRemovable
     */
    public function setIsRemovable(bool $isRemovable): void
    {
        $this->isRemovable = $isRemovable;
    }

    /**
     * Assign model id to image
     *
     * @throws Exception
     */
    private function updateImages()
    {
        Yii::$app->db->createCommand()->update(
            EntityToFile::tableName(),
            ['entity_model_id' => $this->id],
            'temp_sign = :ts', [':ts' => $this->target_sign]
        )->execute();
    }

    /**
     * List of all file attributes. MUST have the following syntax:
     * ```
     * return [
     *      ...,
     *      `attributeName` => self::ATTRIBUTE_FILE_CONSTANT,
     *      `image` => self::SAVE_ATTRIBUTE_IMAGE,
     *      ...
     * ];
     * ```
     *
     * @return array
     */
    public function getUploadAttributes(): array
    {

        return [];
    }

    /**
     * Fetch uploaded files ids. Used for assigning upload attributes to builder model.
     *
     * @param string $attribute
     *
     * @return null|string Json encoded array of file IDs
     * @throws InvalidConfigException
     */
    private function uploadedFiles(string $attribute, bool $check): ?string
    {


        $uploads = $this->uploads;
        if ($uploads === null) {
            \Yii::$app->db->createCommand()
                ->update(
                    EntityToFile::tableName(),
                    [
                        'entity_model_id' => $this->id,
                    ],
                    'temp_sign = :ts AND attribute = :at',
                    [':ts' => $this->target_sign, ':at' => $this->getUploadAttributes()[$attribute] ?? null]
                )
                ->execute();
            $uploads = EntityToFile::find()
                ->select(['file_id'])
                ->andWhere([
                    'entity_model_name' => $this->formName(),
                    'entity_model_id' => $this->id,
                    'attribute' => $this->getUploadAttributes()[$attribute] ?? null,
                    'temp_sign' => $this->target_sign,
                ])->orderBy(['position'=>SORT_DESC])->column();

        }


        return json_encode($uploads);
    }

    /**
     * @return array
     * @todo doc
     */
    public function getArrayAttributes(): array
    {
        return [];
    }

    /**
     * @param $items
     * @return mixed
     * @todo doc
     */
    protected function getItems($items)
    {
        if (is_array($this->$items)) {
            return $this->$items;
        }
        return Json::decode($this->$items, true);
    }

    /**
     * Get MultipleInput image src.
     * @param $imageName
     * @return string
     */
    public static function getImageSrcByName($imageName)
    {
        return '/uploads/multiple-input/' . $imageName;
    }
}
