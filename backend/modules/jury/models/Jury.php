<?php

namespace backend\modules\jury\models;

use backend\modules\imagesUpload\validators\ImageRequireValidator;
use common\models\Expert as CommonExpert;
use common\validators\FileRequiredValidator;
use Yii;
use common\models\Jury as CommonJury;
use backend\components\FormBuilder;
use backend\components\BackendModel;
use backend\components\grid\TranslateColumn;
use backend\components\grid\StylingActionColumn;
use backend\modules\imagesUpload\widgets\imagesUpload\ImageUpload;
use common\models\base\EntityToFile;


/**
 * This is the model class for table "{{%jury}}".
 *
 * @property integer $id
 * @property integer $published
 * @property integer $position
 * @property integer $created_at
 * @property integer $updated_at
 */
class Jury extends CommonJury implements BackendModel
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
            [['name', 'description'], 'required'],

            [['name'], 'string', 'max' => 255],
            [['description'], 'string'],

            [['published'], 'boolean'],
            [['published'], 'default', 'value' => 1],

            [['position'], 'integer'],
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
            'id' => Yii::t('back/jury', 'ID'),
            'published' => Yii::t('back/jury', 'Published'),
            'position' => Yii::t('back/jury', 'Position'),
            'created_at' => Yii::t('back/jury', 'Created At'),
            'updated_at' => Yii::t('back/jury', 'Updated At'),
            'name' => Yii::t('back/jury', 'Name'),
            'description' => Yii::t('back/jury', 'Description'),
            'photo' => Yii::t('back/jury', 'Photo'),
        ];
    }

    /**
     * Get title for the template page
     *
     * @return string
     */
    public function getTitle()
    {
        return Yii::t('back/jury', 'Jury');
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
                    'published:boolean',
                    'position',
                    ['class' => TranslateColumn::class],
                    ['class' => StylingActionColumn::class],
                ];
                break;
            case 'view':
                return [
                    'id',
                    'name',
                    'description',
                    'published:boolean',
                    'position',
                ];
                break;
        }

        return [];
    }

    /**
     * @return JurySearch
     */
    public function getSearchModel()
    {
        return new JurySearch();
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getFormConfig()
    {
        return [
            Yii::t('back/app', 'Main') => [
                'photo' => [
                    'type' => FormBuilder::INPUT_RAW,
                    'value' => ImageUpload::widget([
                        'model' => $this,
                        'attribute' => 'photo',
                        'saveAttribute' => CommonJury::SAVE_ATTRIBUTE_PHOTO,
                        'aspectRatio' => false,
                        'multiple' => false,
                        'allowedFileExtensions' => IMAGE_VALID_FORMATS,
                        'maxFileSize' => MAX_IMAGE_KB
                    ])
                ],
                'name' => [
                    'type' => FormBuilder::INPUT_TEXT,
                ],
                'description' => [
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
     * @throws \yii\base\InvalidConfigException
     */
    public function afterDelete()
    {
        parent::afterDelete();

        EntityToFile::deleteImages($this->formName(), $this->id);
    }
}
