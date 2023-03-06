<?php

namespace backend\modules\layout\models;

use common\validators\FileRequiredValidator;
use Yii;
use common\models\Social as CommonSocial;
use backend\components\FormBuilder;
use backend\components\BackendModel;
use backend\components\grid\StylingActionColumn;
use backend\modules\imagesUpload\widgets\imagesUpload\ImageUpload;
use common\models\base\EntityToFile;


/**
 * This is the model class for table "{{%social}}".
 *
 * @property integer $id
 * @property string $link
 * @property integer $published
 * @property integer $position
 * @property integer $created_at
 * @property integer $updated_at
 */
class Social extends CommonSocial implements BackendModel
{
    /**
     * Attribute for imageUploader
     */
    public $icon;

    /**
     * Temporary sign which used for saving images before model save
     *
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
            [['link'], 'required'],
            [['link'], 'string', 'max' => 255],
            [['link'], 'url', 'defaultScheme' => 'http'],

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
            'id' => Yii::t('back/social', 'ID'),
            'link' => Yii::t('back/social', 'Link'),
            'published' => Yii::t('back/social', 'Published'),
            'position' => Yii::t('back/social', 'Position'),
            'created_at' => Yii::t('back/social', 'Created At'),
            'updated_at' => Yii::t('back/social', 'Updated At'),
            'icon' => Yii::t('back/social', 'Icon'),
        ];
    }

    /**
     * Get title for the template page
     *
     * @return string
     */
    public function getTitle()
    {
        return Yii::t('back/social', 'Social');
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
                    'link',
                    'published:boolean',
                    'position',
                    ['class' => StylingActionColumn::class],
                ];
                break;
            case 'view':
                return [
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
     * @return SocialSearch
     */
    public function getSearchModel()
    {
        return new SocialSearch();
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
                        'saveAttribute' => CommonSocial::SAVE_ATTRIBUTE_ICON,
                        'aspectRatio' => false,
                        'multiple' => false,
                        'allowedFileExtensions' => ['jpg', 'jpeg', 'png'],
                    ])
                ],
                'link' => [
                    'type' => FormBuilder::INPUT_TEXT,
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
