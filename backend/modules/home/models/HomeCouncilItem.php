<?php

namespace backend\modules\home\models;

use backend\widgets\Editor;
use common\validators\FileRequiredValidator;
use Yii;
use common\models\HomeCouncilItem as CommonHomeCouncilItem;
use backend\components\FormBuilder;
use backend\components\BackendModel;
use backend\components\grid\TranslateColumn;
use backend\components\grid\StylingActionColumn;
use backend\modules\imagesUpload\widgets\imagesUpload\ImageUpload;
use common\models\base\EntityToFile;


/**
 * This is the model class for table "{{%home_council_item}}".
 *
 * @property integer $id
 * @property integer $published
 * @property integer $position
 * @property integer $created_at
 * @property integer $updated_at
 */
class HomeCouncilItem extends CommonHomeCouncilItem implements BackendModel
{
    /**
     * Attribute for imageUploader
     */
    public $photo;

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
            [['published'], 'boolean'],
            [['position'], 'integer'],
            [['label'], 'required'],
            [['description'], 'string'],
            [['label'], 'string', 'max' => 255],
            [['published'], 'default', 'value' => 1],
            [['position'], 'default', 'value' => 0],
            [['sign'], 'string', 'max' => 255],

            [
                ['photo'],
                FileRequiredValidator::class,
                'saveAttribute' => self::SAVE_ATTRIBUTE_PHOTO,
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
            'published' => Yii::t('back/home', 'Published'),
            'position' => Yii::t('back/home', 'Position'),
            'created_at' => Yii::t('back/home', 'Created At'),
            'updated_at' => Yii::t('back/home', 'Updated At'),
            'label' => Yii::t('back/home', 'Label'),
            'description' => Yii::t('back/home', 'Description'),
            'photo' => Yii::t('back/home', 'Photo'),
        ];
    }

    /**
     * Get title for the template page
     *
     * @return string
     */
    public function getTitle()
    {
        return Yii::t('back/home', 'Home Council Item');
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
                    ['class' => StylingActionColumn::class],
                ];
                break;
            case 'view':
                return [
                    'label',
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
     * @return HomeCouncilItemSearch
     */
    public function getSearchModel()
    {
        return new HomeCouncilItemSearch();
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
                'description' => [
                    'type' => FormBuilder::INPUT_TEXTAREA,
                ],
                'photo' => [
                    'type' => FormBuilder::INPUT_RAW,
                    'value' => ImageUpload::widget([
                        'model' => $this,
                        'attribute' => 'photo',
                        'saveAttribute' => CommonHomeCouncilItem::SAVE_ATTRIBUTE_PHOTO,
                        'aspectRatio' => false,
                        'multiple' => false,
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
     * @throws \yii\base\InvalidConfigException
     */
    public function afterDelete()
    {
        parent::afterDelete();

        EntityToFile::deleteImages($this->formName(), $this->id);
    }
}
