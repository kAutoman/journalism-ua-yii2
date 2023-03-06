<?php

namespace common\modules\seo\models;

use backend\modules\imagesUpload\widgets\imagesUpload\ImageUpload;
use common\models\EntityToFile;
use Yii;
use yii\db\ActiveQuery;
use common\components\model\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%meta_tags_content}}".
 *
 * @property integer $id
 * @property string $entity_class
 * @property integer $entity_id
 * @property string $language
 * @property string $tag_name
 * @property string $value
 *
 * @property MetaTags $tag
 * @property EntityToFile $image
 */
class MetaTagsContent extends ActiveRecord
{

    public $sign;

    public $tags = [];

    public $writeLog = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%meta_tags_content}}';
    }

    public function init()
    {
        $this->setTags(MetaTags::getMetaTagsList());
        $this->setAttributes($this->tags);

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['entity_class', 'entity_id', 'language', 'tag_name'], 'required'],
            [['entity_id'], 'integer'],
            [['value'], 'string', 'max' => 65535],
            [['entity_class', 'language'], 'string', 'max' => 255],
            [['tag_name'], 'exist', 'targetClass' => MetaTags::class, 'targetAttribute' => 'name'],
            [['sign'], 'safe']
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('back/seo-tags', 'ID'),
            'entity_class' => Yii::t('back/seo-tags', 'Class'),
            'entity_id' => Yii::t('back/seo-tags', 'Entity id'),
            'language' => Yii::t('back/seo-tags', 'Language'),
            'tag_id' => Yii::t('back/seo-tags', 'Tag'),
            'value' => Yii::t('back/seo-tags', 'Value'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getTag()
    {
        return $this->hasOne(MetaTags::class, ['name' => 'tag_name']);
    }

    /**
     * @param array $tags
     */
    public function setTags(array $tags = [])
    {
        foreach ($tags as $name => $value) {
            if (is_int($name)) {
                $this->tags[$value] = null;
            } else {
                $this->tags[$name] = $value;
            }
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImage()
    {
        $entityTable = EntityToFile::tableName();
        return $this->hasOne(EntityToFile::class, ['entity_model_id' => 'id'])
            ->andOnCondition(["{$entityTable}.attribute" => MetaTags::OPEN_GRAPH_IMAGE_ATTRIBUTE]);
    }

    /**
     * @return array
     */
    public function getFormConfig()
    {
        $fields = MetaTags::getAllTags();
        $config = [];
        foreach ($fields as $field) {
            if ($field->type === $field::TYPE_IMAGE) {
                $config[$field->name] = [
                    'type' => $field->getFormType(),
                    'widgetClass' => ImageUpload::class,
                    'options' => [
                        'model' => $field,
                        'attribute' => 'name',
                        'saveAttribute' => $field::OPEN_GRAPH_IMAGE_ATTRIBUTE,
                        'allowedFileExtensions' => ['png', 'jpg', 'jpeg'],
                        'multiple' => false,
                        'maxFileSize' => 500,
                        'webp' => false,
                        'showMetaDataBtn' => false
                    ]
                ];
            } else {
                $config[$field->name] = ['type' => $field->getFormType()];
            }
        }

//        d($config);
        return $config;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->tags)) {
            return $this->tags[$name];
        } else {
            return parent::__get($name);
        }
    }

    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->tags)) {
            $this->tags[$name] = $value;
        } else {
            parent::__set($name, $value);
        }
    }
}
