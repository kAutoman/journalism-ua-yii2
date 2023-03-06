<?php

namespace backend\modules\winner\models;

use common\validators\FileRequiredValidator;
use Yii;
use common\models\WinnerList as CommonWinnerList;
use backend\components\FormBuilder;
use backend\components\BackendModel;
use backend\components\grid\TranslateColumn;
use backend\components\grid\StylingActionColumn;
use backend\modules\imagesUpload\widgets\imagesUpload\ImageUpload;
use common\models\base\EntityToFile;


/**
 * Class WinnerList
 * @package backend\modules\winner\models
 */
class WinnerList extends CommonWinnerList implements BackendModel
{
    /**
     * Attribute for imageUploader
     */
    public $image;

    /**
     * Attribute for imageUploader
     */
    public $file;

    /**
     * Temporary sign which used for saving images before model save
     * @var string
     */
    public $sign;

    /**
     * @throws \yii\base\Exception
     */
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
            [['member_item_id', 'name', 'publication_label', 'publication_link'], 'required'],
            [['member_item_id', 'position'], 'integer'],
            [['published'], 'boolean'],
            [['publication_link', 'name', 'publication_label'], 'string', 'max' => 255],
            [['member_item_id'], 'exist', 'targetClass' => \common\models\MemberItem::class, 'targetAttribute' => 'id'],
            [['published'], 'default', 'value' => 1],
            [['position'], 'default', 'value' => 0],
            [['sign'], 'string', 'max' => 255],
            [
                ['image'],
                FileRequiredValidator::class,
                'saveAttribute' => self::SAVE_ATTRIBUTE_IMAGE,
                'skipOnEmpty' => false
            ],
            [
                ['file'],
                FileRequiredValidator::class,
                'saveAttribute' => self::SAVE_ATTRIBUTE_FILE,
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
            'id' => Yii::t('back/winner', 'ID'),
            'member_item_id' => Yii::t('back/winner', 'Member'),
            'publication_link' => Yii::t('back/winner', 'Publication link'),
            'published' => Yii::t('back/winner', 'Published'),
            'position' => Yii::t('back/winner', 'Position'),
            'created_at' => Yii::t('back/winner', 'Created At'),
            'updated_at' => Yii::t('back/winner', 'Updated At'),
            'name' => Yii::t('back/winner', 'Name'),
            'publication_label' => Yii::t('back/winner', 'Publication label'),
            'image' => Yii::t('back/winner', 'Image'),
            'file' => Yii::t('back/winner', 'File'),
        ];
    }

    /**
     * Get title for the template page
     *
     * @return string
     */
    public function getTitle()
    {
        return Yii::t('back/winner', 'Winner List');
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
                    'name',
//                    'publication_label',
//                    'member_item_id',
//                    'publication_link',
                    'published:boolean',
                    'position',
                    ['class' => TranslateColumn::class],
                    ['class' => StylingActionColumn::class],
                ];
                break;
            case 'view':
                return [
                    'name',
                    'publication_label',
                    'id',
                    'member_item_id',
                    'publication_link',
                    'published:boolean',
                    'position',
                ];
                break;
        }

        return [];
    }

    /**
     * @return WinnerListSearch
     */
    public function getSearchModel()
    {
        return new WinnerListSearch();
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getFormConfig()
    {
        return [
            Yii::t('back/app', 'Main') => [
                'name' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'publication_label' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'member_item_id' => [
                    'type' => FormBuilder::INPUT_DROPDOWN_LIST,
                    'items' => \common\models\MemberItem::getListItems(),
                    'options' => [
                        'prompt' => '',
                    ],
                ],
                'publication_link' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'image' => [
                    'type' => FormBuilder::INPUT_RAW,
                    'value' => ImageUpload::widget([
                        'model' => $this,
                        'attribute' => 'image',
                        'saveAttribute' => CommonWinnerList::SAVE_ATTRIBUTE_IMAGE,
                        'aspectRatio' => false,
                        'multiple' => false,
                        'allowedFileExtensions' => IMAGE_VALID_FORMATS,
                        'maxFileSize' => MAX_IMAGE_KB,
                    ])
                ],
                'file' => [
                    'type' => FormBuilder::INPUT_RAW,
                    'value' => ImageUpload::widget([
                        'model' => $this,
                        'attribute' => 'file',
                        'saveAttribute' => CommonWinnerList::SAVE_ATTRIBUTE_FILE,
                        'aspectRatio' => false,
                        'multiple' => false,
                        'allowedFileExtensions' => DOC_VALID_FORMATS,
                        'maxFileSize' => MAX_BIG_DOC_KB,
                    ])
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
