<?php

namespace backend\modules\home\models;

use common\validators\FileRequiredValidator;
use Yii;
use common\models\HomeHeaderSlider as CommonHomeHeaderSlider;
use backend\components\FormBuilder;
use backend\components\BackendModel;
use backend\components\grid\TranslateColumn;
use backend\components\grid\StylingActionColumn;
use backend\modules\imagesUpload\widgets\imagesUpload\ImageUpload;
use common\models\base\EntityToFile;


/**
 * This is the model class for table "{{%home_header_slider}}".
 *
 * @property integer $id
 * @property integer $button_form_enable
 * @property integer $published
 * @property integer $position
 * @property integer $created_at
 * @property integer $updated_at
 */
class HomeHeaderSlider extends CommonHomeHeaderSlider implements BackendModel
{
    /**
     * Attribute for imageUploader
     */
    public $image;

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
            [['button_form_enable', 'published'], 'boolean'],
            [['position'], 'integer'],
            [['label', 'content'], 'required'],
            [['content'], 'string'],
            [['label', 'button_label', 'button_src'], 'string', 'max' => 255],
            [['published'], 'default', 'value' => 1],
            [['position'], 'default', 'value' => 0],
            [['sign'], 'string', 'max' => 255],

            [
                ['image'],
                FileRequiredValidator::class,
                'saveAttribute' => self::SAVE_ATTRIBUTE_IMAGE,
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
            'id' => Yii::t('back/home', 'ID'),
            'button_form_enable' => Yii::t('back/home', 'Button form enable'),
            'published' => Yii::t('back/home', 'Published'),
            'position' => Yii::t('back/home', 'Position'),
            'created_at' => Yii::t('back/home', 'Created At'),
            'updated_at' => Yii::t('back/home', 'Updated At'),
            'label' => Yii::t('back/home', 'Label'),
            'content' => Yii::t('back/home', 'Content'),
            'button_label' => Yii::t('back/home', 'Button label'),
            'button_src' => Yii::t('back/home', 'Button link'),
            'image' => Yii::t('back/home', 'Image'),
        ];
    }

    /**
     * Get title for the template page
     *
     * @return string
     */
    public function getTitle()
    {
        return Yii::t('back/home', 'Home Header Slider');
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
//                    'button_label',
//                    'button_src',
//                    'button_form_enable',
                    'published:boolean',
                    'position',
                    ['class' => TranslateColumn::class],
                    ['class' => StylingActionColumn::class],
                ];
                break;
            case 'view':
                return [
                    'label',
                    'content',
                    'button_label',
                    'button_src',
                    'id',
                    'button_form_enable',
                    'published:boolean',
                    'position',
                ];
                break;
        }

        return [];
    }

    /**
     * @return HomeHeaderSliderSearch
     */
    public function getSearchModel()
    {
        return new HomeHeaderSliderSearch();
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getFormConfig()
    {
        return [
            Yii::t('back/app', 'Main') => [
                'label' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'content' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'image' => [
                    'type' => FormBuilder::INPUT_RAW,
                    'value' => ImageUpload::widget([
                        'model' => $this,
                        'attribute' => 'image',
                        'saveAttribute' => CommonHomeHeaderSlider::SAVE_ATTRIBUTE_IMAGE,
                        'aspectRatio' => false,
                        'multiple' => false,
                    ])
                ],
                'button_label' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'button_src' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'button_form_enable' => [
                    'type' => FormBuilder::INPUT_CHECKBOX,
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
     * @throws \yii\base\InvalidConfigException
     */
    public function afterDelete()
    {
        parent::afterDelete();

        EntityToFile::deleteImages($this->formName(), $this->id);
    }
}
