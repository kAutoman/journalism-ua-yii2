<?php

namespace common\models;

use common\helpers\UrlHelper;
use common\modules\builder\blocks\article\ArticleListBlock;
use common\modules\builder\blocks\experts\ExpertsHeaderBlock;
use common\modules\builder\blocks\experts\ExpertsListBlock;
use common\modules\builder\blocks\home\HomeCouncilBlock;
use common\modules\builder\blocks\home\HomeDownloadBlock;
use common\modules\builder\blocks\home\HomeHeaderSliderBlock;
use common\modules\builder\blocks\home\HomeNominationBlock;
use common\modules\builder\blocks\home\HomePartnerBlock;
use common\modules\builder\blocks\home\HomeTargetBlock;
use common\modules\builder\blocks\home\HomeTextImageBlock;
use common\modules\builder\blocks\jury\JuryHeaderBlock;
use common\modules\builder\blocks\jury\JuryListBlock;
use common\modules\builder\blocks\member\MemberHeaderBlock;
use common\modules\builder\blocks\member\MemberNominationBlock;
use common\modules\builder\blocks\member\MemberRegisterBlock;
use common\modules\builder\blocks\member\MemberTimelineBlock;
use common\modules\builder\blocks\nominations\HeaderNominationBlock;
use common\modules\builder\blocks\nominations\ListNominationBlock;
use common\modules\builder\blocks\NotFoundBlock;
use common\modules\builder\blocks\NumberTextBlock;
use common\modules\builder\blocks\participants\ParticipantsNominationBlock;
use common\modules\builder\blocks\submit\FormSubmitBlock;
use common\modules\builder\blocks\submit\HeaderSubmitBlock;
use common\modules\builder\blocks\WinnerListBlock;
use common\modules\seo\behaviors\MetaTagsBehavior;
use himiklab\sitemap\behaviors\SitemapBehavior;
use yii\behaviors\TimestampBehavior;
use lav45\translate\TranslatedTrait;
use paulzi\nestedsets\NestedSetsBehavior;
use common\models\lang\PageLang;
use common\interfaces\Translatable;
use common\models\query\PageQuery;
use common\behaviors\TranslatedBehavior;
use common\components\model\ActiveRecord;
use common\modules\builder\behaviors\BuilderBehavior;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{%page}}".
 *
 * @property integer $id
 * @property integer $type
 * @property integer $root
 * @property integer $lft
 * @property integer $rgt
 * @property integer $depth
 * @property string $entity_id
 * @property string $label
 * @property string $alias
 * @property string $content
 * @property integer $published
 * @property integer $movable_u
 * @property integer $movable_d
 * @property integer $movable_l
 * @property integer $movable_r
 * @property integer $removable
 * @property integer $child_allowed
 * @property integer $lock
 * @property integer $deleted
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted_at
 *
 * @property array $builderContent
 * @property \common\modules\seo\models\MetaTagsContent[] $metaTags
 */
class Page extends ActiveRecord implements Translatable
{
    use TranslatedTrait;

    const TYPE_BASIC = 0;
    const TYPE_STATIC_BUILDER = 1;
    const TYPE_DYNAMIC_BUILDER = 2;

    const HOME_PAGE_ID = 1;
    const PRIVACY_POLICY_PAGE_ID = 2;
    const ERROR_PAGE_ID = 3;

    /**
     * List if permanent Pages that cannot be deleted, be unpublished etc.
     *
     * @var array
     */
    public static $permanentPages = [
        self::HOME_PAGE_ID,
        self::PRIVACY_POLICY_PAGE_ID,
        self::ERROR_PAGE_ID,
    ];


    public $langModelClass = PageLang::class;

    public $builderContent;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%page}}';
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function find()
    {
        return new PageQuery(get_called_class());
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
            'tree' => [
                'class' => NestedSetsBehavior::class,
                'treeAttribute' => 'root',
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
                    $model->andWhere(['deleted' => 0, 'published' => 1]);
                    $model->andWhere(['!=', 'alias', '404']);
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
                'mode' => function (self $model) {
                    if ($model->type === $model::TYPE_STATIC_BUILDER) {
                        return BuilderBehavior::MODE_TYPE_STATIC;
                    }
                    return BuilderBehavior::MODE_TYPE_DYNAMIC;
                },
                'models' => [
                    bt('Common', 'builder') => [
                        HomeDownloadBlock::class,
                        NumberTextBlock::class,
                    ],
                    bt('Home page', 'builder') => [
                        HomeHeaderSliderBlock::class,
                        HomeTargetBlock::class,
                        HomeTextImageBlock::class,
                        HomeNominationBlock::class,
                        HomeCouncilBlock::class,
                        HomePartnerBlock::class,
                    ],
                    bt('Experts page', 'builder') => [
                        ExpertsHeaderBlock::class,
                        ExpertsListBlock::class,
                    ],
                    bt('Nomination page', 'builder') => [
                        HeaderNominationBlock::class,
                        ListNominationBlock::class,
                    ],
                    bt('Participants page', 'builder') => [
                        ParticipantsNominationBlock::class,
                    ],
                    bt('Jury page', 'builder') => [
                        JuryHeaderBlock::class,
                        JuryListBlock::class,
                    ],
                    bt('Member page', 'builder') => [
                        MemberHeaderBlock::class,
                        MemberRegisterBlock::class,
                        MemberTimelineBlock::class,
                        MemberNominationBlock::class,
                    ],
                    bt('Winner page', 'builder') => [
                        WinnerListBlock::class,
                    ],
                    bt('Article page', 'builder') => [
                        ArticleListBlock::class,
                    ],
                    bt('Not found page', 'builder') => [
                        NotFoundBlock::class,
                    ],
                    bt('Form page', 'builder') => [
                        HeaderSubmitBlock::class,
                        FormSubmitBlock::class,
                    ],
                ]
            ]
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
            'content',
        ];
    }

    /**
     * @return array
     */
    public static function getPageTypeList(): array
    {
        return [
            self::TYPE_BASIC => bt('Basic', 'page'),
            self::TYPE_STATIC_BUILDER => bt('Static builder', 'page'),
            self::TYPE_DYNAMIC_BUILDER => bt('Dynamic builder', 'page'),
        ];
    }

    public static function getUrl(array $params = [], bool $schema = false): string
    {
        return UrlHelper::createUrl('/page/page/index', $params, $schema);
    }

    /**
     * @param int $type
     *
     * @return string|null
     */
    public static function getPageType(int $type): ?string
    {
        return self::getPageTypeList()[$type] ?? null;
    }

    /**
     * @return bool
     */
    public function isHome(): bool
    {
        return $this->id === self::HOME_PAGE_ID;
    }

    /**
     * @return bool
     */
    public function isPermanent(): bool
    {
        return in_array($this->id, self::$permanentPages);
    }
}
