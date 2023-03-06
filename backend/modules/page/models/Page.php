<?php

namespace backend\modules\page\models;

use common\modules\builder\behaviors\BuilderBehavior;
use common\modules\seo\behaviors\MetaTagsBehavior;
use Yii;
use yii\db\Exception;
use yii\helpers\Inflector;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use yii\behaviors\OptimisticLockBehavior;
use unclead\multipleinput\MultipleInput;
use common\helpers\Pattern;
use common\helpers\LanguageHelper;
use common\models\Page as CommonPage;
use common\behaviors\SluggableBehavior;
use common\modules\builder\models\Builder;
use common\modules\builder\widgets\BuilderForm;
use backend\widgets\Editor;
use backend\components\FormBuilder;
use backend\components\BackendModel;
use backend\components\grid\TranslateColumn;
use backend\components\grid\StylingActionColumn;

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
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @method BuilderBehavior getFormattedModels()
 */
class Page extends CommonPage implements BackendModel
{
    public $builderInit;

    public $updatePage = false;

    public function optimisticLock()
    {
        return 'lock';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            [['type', 'root'], 'integer'],
            [['label'], 'required'],
            [['content'], 'string', 'max' => 65535],
            [
                ['published', 'movable_u', 'movable_d', 'movable_l', 'movable_r', 'removable', 'child_allowed'],
                'boolean'
            ],
            [['label', 'alias'], 'string', 'max' => 255],
            [
                ['published', 'movable_u', 'movable_d', 'movable_l', 'movable_r', 'removable', 'child_allowed'],
                'default',
                'value' => 1
            ],
            [['lock'], 'default', 'value' => 0],

            [['lft', 'rgt', 'depth', 'lock', 'entity_id', 'builderInit'], 'safe']
        ];

        if (!$this->isHome()) {
            $rules = array_merge($rules, [
                [['alias'], 'match', 'pattern' => Pattern::alias()],
                [['alias'], 'unique'],
            ]);
        }

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'sluggableBehavior' => SluggableBehavior::class,
            'lock' => OptimisticLockBehavior::class,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('back/page', 'ID'),
            'type' => Yii::t('back/page', 'Type'),
            'root' => Yii::t('back/page', 'Root'),
            'lft' => Yii::t('back/page', 'Lft'),
            'rgt' => Yii::t('back/page', 'Rgt'),
            'depth' => Yii::t('back/page', 'Depth'),
            'label' => Yii::t('back/page', 'Label'),
            'alias' => Yii::t('back/page', 'Alias'),
            'content' => Yii::t('back/page', 'Content'),
            'published' => Yii::t('back/page', 'Published'),
            'movable_u' => Yii::t('back/page', 'Movable U'),
            'movable_d' => Yii::t('back/page', 'Movable D'),
            'movable_l' => Yii::t('back/page', 'Movable L'),
            'movable_r' => Yii::t('back/page', 'Movable R'),
            'removable' => Yii::t('back/page', 'Removable'),
            'child_allowed' => Yii::t('back/page', 'Child Allowed'),
            'lock' => Yii::t('back/page', 'Lock'),
            'created_at' => Yii::t('back/page', 'Created At'),
            'updated_at' => Yii::t('back/page', 'Updated At'),
            'builderInit' => Yii::t('back/page', 'Init blocks'),
        ];
    }

    /**
     * Get title for the template page
     *
     * @return string
     */
    public function getTitle()
    {
        return Yii::t('back/page', 'Page');
    }

    /**
     * Get attribute columns for index and view page
     *
     * @param $page
     *
     * @return array
     */
    public function getColumns($page)
    {
//        d(self::find()->roots()->all());
        switch ($page) {
            case 'index':
                return [
                    ['class' => 'yii\grid\SerialColumn'],
                    'type',
                    'root',
                    'lft',
                    'rgt',
                    'depth',
                    'label',
                    'alias',
                    'published:boolean',
                    'movable_u',
                    'movable_d',
                    'movable_l',
                    'movable_r',
                    'removable',
                    'child_allowed',
                    'lock',
                    ['class' => TranslateColumn::class],
                    ['class' => StylingActionColumn::class],
                ];
                break;
            case 'view':
                return [
                    'id',
                    'type',
                    'root',
                    'lft',
                    'rgt',
                    'depth',
                    'label',
                    'alias',
                    'content',
                    'published:boolean',
                    'movable_u',
                    'movable_d',
                    'movable_l',
                    'movable_r',
                    'removable',
                    'child_allowed',
                    'lock',
                ];
                break;
        }

        return [];
    }

    /**
     * @return PageSearch
     */
    public function getSearchModel()
    {
        return new PageSearch();
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getCreateForm()
    {
        return [
            Yii::t('back/app', 'Main') => [
                'type' => [
                    'type' => FormBuilder::INPUT_DROPDOWN_LIST,
                    'items' => self::getPageTypeList(),
                    'options' => ['id' => 'page-type']
                ],
                'label' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'alias' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'builderInit' => [
                    'type' => FormBuilder::INPUT_RAW,
                    'value' => MultipleInput::widget([
                        'id' => 'builder-init',
                        'model' => $this,
                        'attribute' => 'builderInit',
                        'sortable' => true,
                        'min' => 1, // should be at least 1 row
                        'addButtonPosition' => MultipleInput::POS_HEADER, // show add button in the header
                        'enableError' => true,
                        'columns' => [

                            [
                                'name' => 'className',
                                'type' => FormBuilder::INPUT_DROPDOWN_LIST,
                                'items' => $this->getFormattedModels(),
                                'title' => 'Block',
                            ],
                            [
                                'name' => 'componentName',
                                'type' => FormBuilder::INPUT_TEXT,
                                'title' => 'Custom component name',
                            ],
                        ]
                    ])

                ],
                'lock' => [
                    'type' => FormBuilder::INPUT_HIDDEN,
                    'label' => false
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function getFormConfig()
    {
        $tabs[Yii::t('back/app', 'Main')] = [
//            'type' => [
//                'type' => FormBuilder::INPUT_DROPDOWN_LIST,
//                'items' => self::getPageTypeList()
//            ],
            'label' => [
                'type' => FormBuilder::INPUT_TEXT,
            ],
            'alias' => [
                'type' => FormBuilder::INPUT_TEXT,
                'options' => ['readonly' => $this->isPermanent()]
            ],
            'lock' => [
                'type' => FormBuilder::INPUT_HIDDEN,
                'label' => false
            ],
        ];

        if (!$this->isPermanent()) {
            $tabs[Yii::t('back/app', 'Main')]['published'] = [
                'type' => FormBuilder::INPUT_CHECKBOX
            ];
        }

        if ($this->type === self::TYPE_BASIC) {
            $tabs[Yii::t('back/app', 'Content')] = [
                'content' => [
                    'type' => FormBuilder::INPUT_WIDGET,
                    'widgetClass' => Editor::class,
                ],
            ];
        } else {
            $tabs[Yii::t('back/app', 'Content')] = [
                'builderContent' => [
                    'type' => FormBuilder::INPUT_WIDGET,
                    'widgetClass' => BuilderForm::class,
                    'label' => false,
                    'options' => [
                        'model' => $this,
                        'attribute' => 'builderContent',
                    ]
                ],
            ];
        }

        return $tabs;
    }

    /**
     * @throws InvalidConfigException
     * @throws Exception
     */
    private function insertPredefinedBlocks()
    {
        $defaultLang = LanguageHelper::getEditLanguage();
        $time = time();

        $rows = [];

        foreach ($this->builderInit as $position => $block) {
            $rows[] = [
                'language' => $defaultLang,
                'builder_model_class' => $block['className'],
                'target_class' => self::formName(),
                'target_id' => !$this->getIsNewRecord() ? $this->id : 0,
                'target_attribute' => 'builderContent',
                'position' => $position,
                'tag_level' => 1,
                'component_name' => $block['componentName'] ?? null,
                'created_at' => $time,
                'updated_at' => $time,
            ];
        }

        Yii::$app->getDb()->createCommand()
            ->batchInsert(Builder::tableName(), [
                'language',
                'builder_model_class',
                'target_class',
                'target_id',
                'target_attribute',
                'position',
                'tag_level',
                'component_name',
                'created_at',
                'updated_at',
            ], $rows)
            ->execute();
    }

    /**
     * @throws InvalidConfigException
     */
    private function removeOldBlocks()
    {
        $defaultLang = LanguageHelper::getEditLanguage();

        Builder::deleteAll([
            'language' => $defaultLang,
            'target_class' => self::formName(),
            'target_id' => $this->id,
            'target_attribute' => 'builderContent'
        ]);
    }

    /**
     * @throws InvalidConfigException
     */
    public function afterFind()
    {
        parent::afterFind();

        if ($this->updatePage) {
            if (((int)$this->type === self::TYPE_STATIC_BUILDER || (int)$this->type === self::TYPE_DYNAMIC_BUILDER)) {
                /** @var Builder[] $builderItems */
                $builderItems = Builder::find()->andWhere([
                    'language' => LanguageHelper::getEditLanguage(),
                    'target_class' => self::formName(),
                    'target_id' => $this->id,
                    'target_attribute' => 'builderContent'
                ])->all();

                foreach ($builderItems as $builderItem) {
                    $this->builderInit[] = [
                        'className' => $builderItem->builder_model_class,
                        'componentName' => $builderItem->component_name,
                    ];
                }
            }
        }
    }

    /**
     * @param bool $insert
     *
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($this->getIsNewRecord()) {
            $this->entity_id = Inflector::camelize($this->alias . 'Page');
        }
        return parent::beforeSave($insert);
    }

    /**
     * @param $insert
     * @param $changedAttributes
     *
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if($this->updatePage) {
            if ($this->builderInit && ((int)$this->type === self::TYPE_STATIC_BUILDER || (int)$this->type === self::TYPE_DYNAMIC_BUILDER)) {
                $this->removeOldBlocks();
            }

            if ($this->builderInit && (int)$this->type === self::TYPE_STATIC_BUILDER) {
                $this->insertPredefinedBlocks();
            }
        }
    }
}
