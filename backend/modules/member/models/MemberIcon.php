<?php

namespace backend\modules\member\models;

use common\validators\FileRequiredValidator;
use Yii;
use common\models\MemberIcon as CommonMemberIcon;
use backend\components\FormBuilder;
use backend\components\BackendModel;
use backend\components\grid\TranslateColumn;
use backend\components\grid\StylingActionColumn;
use backend\modules\imagesUpload\widgets\imagesUpload\ImageUpload;
use common\models\base\EntityToFile;


/**
 * This is the model class for table "{{%member_icon}}".
 *
 * @property integer $id
 * @property integer $published
 * @property integer $position
 * @property integer $created_at
 * @property integer $updated_at
 */
class MemberIcon extends CommonMemberIcon implements BackendModel
{
    /**
     * Attribute for imageUploader
     */
    public $icon;

    /**
     * Temporary sign which used for saving images before model save
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
            [['description'], 'required'],
            [['description'], 'string'],

            [['published'], 'boolean'],
            [['published'], 'default', 'value' => 1],

            [['position'], 'integer'],
            [['position'], 'default', 'value' => 0],

            [['sign'], 'string', 'max' => 255],

            [
                ['icon'],
                FileRequiredValidator::class,
                'saveAttribute' => self::SAVE_ATTRIBUTE_ICON,
                'skipOnEmpty' => false
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
            'description' => Yii::t('back/member', 'Description'),
            'icon' => Yii::t('back/member', 'Icon'),
        ];
    }

    /**
     * Get title for the template page
     *
     * @return string
     */
    public function getTitle()
    {
        return Yii::t('back/member', 'Member Icon');
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
                    'description',
                    'published:boolean',
                    'position',
                    ['class' => TranslateColumn::class],
                    ['class' => StylingActionColumn::class],
                ];
                break;
            case 'view':
                return [
                    'description',
                    'id',
                    'published:boolean',
                    'position',
                ];
                break;
        }

        return [];
    }

    /**
     * @return MemberIconSearch
     */
    public function getSearchModel()
    {
        return new MemberIconSearch();
    }

    /**
     * @return array
     */
    public function getFormConfig()
    {
        return [
            Yii::t('back/app', 'Main') => [
                'icon' => [
                    'type' => FormBuilder::INPUT_RAW,
                    'value' => ImageUpload::widget([
                        'model' => $this,
                        'attribute' => 'icon',
                        'saveAttribute' => CommonMemberIcon::SAVE_ATTRIBUTE_ICON,
                        'aspectRatio' => false,
                        'multiple' => false,
                        'allowedFileExtensions' => IMAGE_VALID_FORMATS,
                        'maxFileSize' => MAX_IMAGE_KB
                    ])
                ],
                'description' => [
                    'type' => FormBuilder::INPUT_TEXTAREA,
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
}
