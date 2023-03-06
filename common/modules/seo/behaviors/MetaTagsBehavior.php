<?php

namespace common\modules\seo\behaviors;

use common\components\model\ActiveRecord;
use common\helpers\LanguageHelper;
use common\modules\seo\models\MetaTags;
use common\modules\seo\models\MetaTagsContent;
use Yii;
use yii\base\Behavior;
use yii\base\InvalidConfigException;

/**
 * Class MetaTagsBehavior
 *
 * @package common\modules\seo\behaviors
 */
class MetaTagsBehavior extends Behavior
{
    /**
     * @var MetaTagsContent
     */
    public $metaTags;

    public $defaultTitleAttribute;
    public $defaultDescriptionAttribute;

    protected $titleAttributes = ['title', 'og_title'];
    protected $descriptionAttributes = ['description', 'og_description'];

    /**
     * @var array
     */
    private $tags = [];

    protected $_language;

    public function init()
    {
        parent::init();
        $this->tags = MetaTags::getMetaTagsList();
        $this->_language = YII_APP === 'admin' ? LanguageHelper::getEditLanguage() : app()->language;
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'saveTags',
            ActiveRecord::EVENT_AFTER_UPDATE => 'saveTags',
            ActiveRecord::EVENT_AFTER_FIND => 'loadTags',
            ActiveRecord::EVENT_AFTER_DELETE => 'removeTags',
        ];
    }

    /**
     * Load exist meta tags after model find
     */
    public function loadTags()
    {
        $this->metaTags = $this->getExistingMetaTags();
    }

    public function saveTags()
    {
        $models = $this->getExistingMetaTags();

        foreach ($models as $tagName => $model) {
            $tag = $this->loadModel($model, $tagName);
            $tag->save();

        }
    }

    public function removeTags()
    {
        Yii::$app->getDb()->createCommand()
            ->delete(MetaTagsContent::tableName(), [
                'entity_id' => $this->owner->id,
                'entity_class' => $this->owner->formName()
            ])
            ->execute();
    }

    /**
     * @return MetaTagsContent[]
     * @throws InvalidConfigException
     */
    protected function getExistingMetaTags()
    {
        /** @var ActiveRecord $model */
        $model = $this->owner;
        $modelName = $model->formName();
        $modelId = $model->id;
//        $tags = array_fill_keys($this->tags, null);

        if ($modelId !== null) {
            $metaTagsContent = MetaTagsContent::find()
                ->indexBy('tag_name')
                ->andWhere(['entity_class' => $modelName, 'entity_id' => $modelId, 'language' => $this->_language])
                ->all();
            foreach ($metaTagsContent as $attribute => $modelContent) {
                $modelContent->{$attribute} = $modelContent->value;
            }
            if (empty($metaTagsContent)) {

                foreach ($this->tags as $tag) {
                    $metaTagsContent[$tag] = new MetaTagsContent([
                        'entity_class' => $modelName,
                        'entity_id' => $modelId,
                        'tag_name' => $tag,
                        'value' => null,
                    ]);
                }
            }

            return $metaTagsContent;
        }

        return null;
    }

    /**
     * @param MetaTagsContent $model
     * @param string $tagName
     * @return MetaTagsContent
     * @throws InvalidConfigException
     */
    public function loadModel(MetaTagsContent $model, string $tagName): MetaTagsContent
    {
        $params = request()->post($model->formName());

        $model->language = $this->_language;
        $model->tag_name = $tagName;
        $model->value = $params[$tagName] ?? null;

        return $model;
    }

    /**
     * @return array
     */
    public function getTitleAttributes()
    {
        return $this->titleAttributes;
    }

    /**
     * @return array
     */
    public function getDescriptionAttributes()
    {
        return $this->descriptionAttributes;
    }
}
