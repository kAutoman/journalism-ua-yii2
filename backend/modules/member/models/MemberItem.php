<?php

namespace backend\modules\member\models;

use backend\modules\rbac\models\User;
use backend\widgets\Editor;
use common\validators\FileRequiredValidator;
use Yii;
use common\models\MemberItem as CommonMemberItem;
use backend\components\FormBuilder;
use backend\components\BackendModel;
use backend\components\grid\TranslateColumn;
use backend\components\grid\StylingActionColumn;
use backend\modules\imagesUpload\widgets\imagesUpload\ImageUpload;
use common\models\base\EntityToFile;
use yii\db\Query;

/**
 * This is the model class for table "{{%member_item}}".
 *
 * @property integer $id
 * @property integer $published
 * @property integer $position
 * @property integer $created_at
 * @property integer $updated_at
 */
class MemberItem extends CommonMemberItem implements BackendModel
{
    /**
     * Attribute for imageUploader
     */
    public $image;

    /**
     * Temporary sign which used for saving images before model save
     *
     * @var string
     */
    public $sign;

    public function init()
    {
        parent::init();

        if (!$this->sign) {
            $this->sign = Yii::$app->getSecurity()->generateRandomString();
        }
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['label'], 'required'],
            [['label'], 'string', 'max' => 255],

            [['content'], 'string', 'max' => 1000],

            [['published'], 'boolean'],
            [['published'], 'default', 'value' => 1],

            [['position'], 'integer'],
            [['position'], 'default', 'value' => 0],

            [['sign'], 'string', 'max' => 255],

            [
                ['image'],
                FileRequiredValidator::class,
                'saveAttribute' => self::SAVE_ATTRIBUTE_IMAGE,
                'skipOnEmpty' => true
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('back/member', 'ID'),
            'published' => Yii::t('back/member', 'Published'),
            'position' => Yii::t('back/member', 'Position'),
            'created_at' => Yii::t('back/member', 'Created At'),
            'updated_at' => Yii::t('back/member', 'Updated At'),
            'label' => Yii::t('back/member', 'Label'),
            'content' => Yii::t('back/member', 'Content'),
            'image' => Yii::t('back/member', 'image'),
        ];
    }

    /**
     * Get title for the template page
     *
     * @return string
     */
    public function getTitle()
    {
        return Yii::t('back/member', 'Member Item');
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
        switch ($page) {
            case 'index':
                return [
                    ['class' => 'yii\grid\SerialColumn'],
                    'label',
                    'published:boolean',
                    'position',
                    ['class' => TranslateColumn::class],
                    [
                        'class' => StylingActionColumn::class,
                        'template' => '{update}'
                    ],
                ];
                break;
            case 'view':
                return [
                    'label',
                    'content',
                    'id',
                    'published:boolean',
                    'position',
                ];
                break;
        }

        return [];
    }

    /**
     * @return MemberItemSearch
     */
    public function getSearchModel()
    {
        return new MemberItemSearch();
    }

    /**
     * @return array
     */
    public function getFormConfig()
    {
        return [
            Yii::t('back/app', 'Main') => [
                'image' => [
                    'type' => FormBuilder::INPUT_RAW,
                    'value' => ImageUpload::widget([
                        'model' => $this,
                        'attribute' => 'image',
                        'saveAttribute' => CommonMemberItem::SAVE_ATTRIBUTE_IMAGE,
                        'aspectRatio' => false,
                        'multiple' => false,
                        'allowedFileExtensions' => ICON_VALID_FORMATS,
                        'maxFileSize' => MAX_IMAGE_KB
                    ])
                ],
                'label' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'content' => [
                    'type' => FormBuilder::INPUT_WIDGET,
                    'widgetClass' => Editor::class,
                    'widgetOptions' => [
                        'model' => $this,
                        'attribute' => 'content'
                    ]
                ],
                'published' => [
                    'type' => FormBuilder::INPUT_CHECKBOX,
                ],
                'position' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'sign' => [
                    'type' => FormBuilder::INPUT_HIDDEN,
                    'label' => false,
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        EntityToFile::updateImages($this->id, $this->sign);
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        parent::afterDelete();

        EntityToFile::deleteImages($this->formName(), $this->id);
    }

    /**
     * @return array
     * @throws \Throwable
     */
    public static function getListByUser()
    {
        // if (user()->can(User::ROLE_JURY) || user()->can(User::ROLE_MODERATOR)) {
        //     /** @var User $user */
        //     $user = user()->getIdentity();
        //     // var_dump($user);

        //     return map($user->memberItems, 'id', 'label');
        // }

        // return map(self::find()->all(), $from, $to);

        return self::getListItems();
    }
}
