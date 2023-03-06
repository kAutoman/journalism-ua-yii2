<?php

namespace backend\modules\language\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\log\Logger;
use yii\helpers\{Html, ArrayHelper};
use common\helpers\LanguageHelper;
use common\components\model\ActiveRecord;
use backend\components\{FormBuilder, BackendModel, grid\StylingActionColumn};
use backend\modules\language\widgets\TranslationFields;

/**
 * This is the model class for table "{{%source_message}}".
 *
 * @property integer $id
 * @property string $category
 * @property string $message
 * @property string $status
 *
 * @property Message[] $messages
 */
class SourceMessage extends ActiveRecord implements BackendModel
{
    const STATUS_NOT_TRANSLATED = 0;
    const STATUS_TRANSLATED = 1;

    /**
     * @var string
     */
    public $status;

    public $translation;

    /**
     * @inheritdoc
     * @return SourceMessageQuery the newly created [[SourceMessageQuery]] instance.
     */
    public static function find()
    {
        return new SourceMessageQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%source_message}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category'], 'required'],
            [['message'], 'string'],
            [['category'], 'string', 'max' => 32],
            [['translation'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('back/translation', 'ID'),
            'category' => Yii::t('back/translation', 'Category'),
            'status' => Yii::t('back/translation', 'Status'),
            'translation' => Yii::t('back/translation', 'Message'),
        ];
    }

    /**
     * Get title for the template page
     *
     * @return string
     */
    public function getTitle()
    {
        return Yii::t('back/translation', 'Source Message');
    }

    /**
     * Get attribute columns for index and view page
     *
     * @param $page
     *
     * @return array
     * @throws InvalidConfigException
     */
    public function getColumns($page)
    {
        return [
            [
                'attribute' => 'id',
                'options' => ['width' => '5%']
            ],
            [
                'attribute' => 'category',
                'filter' => ArrayHelper::map(self::getCategories(), 'category', 'category'),
                'options' => ['width' => '20%']
            ],
            [
                'attribute' => 'translation',
                'format' => 'raw',
                'value' => function (self $model) {
                    $messages = [
                        Html::tag('small', "<strong>Source</strong>: {$model->message}")
                    ];
                    foreach ($model->messages as $message) {
                        if ($message->translation) {
                            $messages[] = Html::tag('small', "<strong><code>{$message->language}:</code></strong> " . $message->translation);
                        }
                    }
                    return implode('<br> ', $messages);
                }
            ],
            [
                'class' => StylingActionColumn::class,
                'template' => '{update}'
            ]
        ];
    }

    /**
     * @return SourceMessageSearch
     */
    public function getSearchModel()
    {
        return new SourceMessageSearch();
    }

    /**
     * @return array
     */
    public function getFormConfig()
    {
        return [
            Yii::t('back/app', 'Main') => [
                'category' => [
                    'type' => FormBuilder::INPUT_TEXT,
                    'options' => ['readonly' => true],
                ],
                'message' => [
                    'type' => FormBuilder::INPUT_TEXT,
                    'options' => ['readonly' => true],
                ],
                'translation' => [
                    'type' => FormBuilder::INPUT_WIDGET,
                    'widgetClass' => TranslationFields::class,
                    'options' => [
                        'model' => $this,
                        'attribute' => 'translation'
                    ]
                ]
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessages()
    {
        return $this->hasMany(Message::class, ['id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDefaultMessage()
    {
        return $this->hasOne(Message::class, ['id' => 'id'])
            ->andOnCondition(['language' => LanguageHelper::getDefaultLanguage()->code]);
    }

    /**
     * @return SourceMessage[]
     */
    public static function getCategories()
    {
        return self::find()->select('category')->distinct('category')->asArray()->all();
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->translation = $this->messages;
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $transaction = Yii::$app->getDb()->beginTransaction();
        foreach ($this->translation as $lang => $value) {
            Yii::$app->getDb()->createCommand()->upsert(
                Message::tableName(),
                [
                    'id' => $this->id,
                    'language' => $lang,
                    'translation' => $value,
                ]
            )->execute();
        }

        try {
            $transaction->commit();
        } catch (Exception $e) {
            Yii::getLogger()->log($e->getMessage(), Logger::LEVEL_ERROR);
            $transaction->rollBack();
        }
        Yii::$app->cacheLang->flush();
    }

    public function beforeDelete()
    {
        Yii::$app->cacheLang->flush();
        parent::afterDelete();
    }
}
