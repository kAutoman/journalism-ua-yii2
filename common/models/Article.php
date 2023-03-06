<?php

namespace common\models;

use common\components\model\ActiveRecord;
use common\modules\builder\behaviors\BuilderBehavior;
use common\modules\builder\models\SampleModel;
use common\modules\seo\behaviors\MetaTagsBehavior;
use himiklab\sitemap\behaviors\SitemapBehavior;
use yii\behaviors\TimestampBehavior;
use common\behaviors\TranslatedBehavior;
use common\interfaces\Translatable;
use common\models\lang\ArticleLang;
use lav45\translate\TranslatedTrait;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{%article}}".
 *
 * @property integer $id
 * @property string $label
 * @property string $alias
 * @property integer $publication_date
 * @property integer $published
 * @property integer $position
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property EntityToFile $preview
 * @property EntityToFile $banner
 */
class Article extends ActiveRecord implements Translatable
{
    const SAVE_ATTRIBUTE_PREVIEW = 'ArticlePreview';
    const SAVE_ATTRIBUTE_BANNER = 'ArticleBanner';

    use TranslatedTrait;

    public $langModelClass = ArticleLang::class;

    public $builderContent;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%article}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
            ],
            'translated' => [
                'class' => TranslatedBehavior::class,
                'translateAttributes' => $this->getLangAttributes()
            ],
            'seo' => [
                'class' => MetaTagsBehavior::class,
                'defaultTitleAttribute' => 'label'
            ],
            'sitemap' => [
                'class' => SitemapBehavior::class,
                'scope' => function ($model) {
                    /** @var ActiveQuery $model */
                    $model->select(['alias', 'updated_at']);
                    $model->andWhere(['published' => 1]);
                },
                'dataClosure' => function ($model) {
                    /** @var self $model */
                    $url = $model::getUrl(['alias' => $model->alias], true);
                    return [
                        'loc' => $url,
                        'lastmod' => strtotime($model->updated_at),
                        'changefreq' => SitemapBehavior::CHANGEFREQ_DAILY,
                        'priority' => 0.8
                    ];
                }
            ],
            'builder' => [
                'class' => BuilderBehavior::class,
                'attribute' => 'builderContent',
                'models' => [
                    bt('Article') => [
                    ],
                ]
            ],
        ];
    }

    /**
     * List of all translatable attributes from
     *
     * @return array
     */
    public function getLangAttributes(): array
    {
        return [
            'label',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getPreview()
    {
        return $this->hasOne(EntityToFile::class, ['entity_model_id' => 'id'])
            ->andOnCondition([
                'preview.entity_model_name' => static::formName(),
                'preview.attribute' => static::SAVE_ATTRIBUTE_PREVIEW
            ])
            ->alias('preview')
            ->orderBy('preview.position DESC');
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getBanner()
    {
        return $this->hasOne(EntityToFile::class, ['entity_model_id' => 'id'])
            ->andOnCondition([
                'banner.entity_model_name' => static::formName(),
                'banner.attribute' => static::SAVE_ATTRIBUTE_BANNER
            ])
            ->alias('banner')
            ->orderBy('banner.position DESC');
    }
}
