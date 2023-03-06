<?php

namespace backend\modules\home\models;

use common\validators\FileRequiredValidator;
use Yii;
use common\models\HomePartnerItem as CommonHomePartnerItem;
use backend\components\FormBuilder;
use backend\components\BackendModel;
use backend\components\grid\TranslateColumn;
use backend\components\grid\StylingActionColumn;
use backend\modules\imagesUpload\widgets\imagesUpload\ImageUpload;
use common\models\base\EntityToFile;


/**
 * This is the model class for table "{{%home_partner_item}}".
 *
 * @property integer $id
 * @property string $link
 * @property integer $published
 * @property integer $position
 * @property integer $created_at
 * @property integer $updated_at
 */
class HomePartnerItem extends CommonHomePartnerItem implements BackendModel
{
    /**
     * Attribute for imageUploader
     */
    public $logo;

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
            [['link', 'label'], 'required'],
            [['published'], 'boolean'],
            [['position'], 'integer'],
            [['link', 'label'], 'string', 'max' => 255],
            [['link'], 'url', 'defaultScheme' => 'http'],
            [['published'], 'default', 'value' => 1],
            [['position'], 'default', 'value' => 0],
            [['sign'], 'string', 'max' => 255],

            [
                ['logo'],
                FileRequiredValidator::class,
                'saveAttribute' => self::SAVE_ATTRIBUTE_LOGO,
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
            'link' => Yii::t('back/home', 'Link'),
            'published' => Yii::t('back/home', 'Published'),
            'position' => Yii::t('back/home', 'Position'),
            'created_at' => Yii::t('back/home', 'Created At'),
            'updated_at' => Yii::t('back/home', 'Updated At'),
            'label' => Yii::t('back/home', 'Label'),
            'logo' => Yii::t('back/home', 'Logo'),
        ];
    }

    /**
     * Get title for the template page
     *
     * @return string
     */
    public function getTitle()
    {
        return Yii::t('back/home', 'Home Partner Item');
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
//                    'link',
                    'published:boolean',
                    'position',
                    ['class' => TranslateColumn::class],
                    ['class' => StylingActionColumn::class],
                ];
                break;
            case 'view':
                return [
                    'label',
                    'id',
                    'link',
                    'published:boolean',
                    'position',
                ];
                break;
        }

        return [];
    }

    /**
     * @return HomePartnerItemSearch
     */
    public function getSearchModel()
    {
        return new HomePartnerItemSearch();
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
                'link' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'logo' => [
                    'type' => FormBuilder::INPUT_RAW,
                    'value' => ImageUpload::widget([
                        'model' => $this,
                        'attribute' => 'logo',
                        'saveAttribute' => CommonHomePartnerItem::SAVE_ATTRIBUTE_LOGO,
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
