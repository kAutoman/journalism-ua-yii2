<?php

namespace common\models;

use common\components\model\ActiveRecord;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%user_log}}".
 *
 * @property integer $id
 * @property string $action
 * @property integer $user_id
 * @property string $model_class
 * @property string $entity_id
 * @property string $content_before
 * @property string $user_info
 * @property string $content_after
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $user
 */
class UserLog extends ActiveRecord
{
    CONST ACTION_CREATE = 'create';
    CONST ACTION_UPDATE = 'update';
    CONST ACTION_DELETE = 'delete';

    public $writeLog = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['action', 'model_class'], 'required'],
            [['user_id'], 'integer'],
            [['entity_id'], 'safe'],
            [['content_before', 'user_info', 'content_after'], 'string'],
            ///[['content_before', 'user_info', 'content_after'], 'filter', 'filter' => 'json_decode'],
            [['action', 'model_class'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'targetClass' => \common\models\User::class, 'targetAttribute' => 'id'],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_log}}';
    }

    public static function encode($data)
    {
        self::_prepareDataToLog($data);
        return $data === null ? '{}' : json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    private static function _prepareDataToLog(&$data)
    {
        if (isset($data['created_at'])) {
            $data['created_at'] = Yii::$app->formatter->asDatetime($data['created_at']);
        }

        if (isset($data['updated_at'])) {
            $data['updated_at'] = Yii::$app->formatter->asDatetime($data['updated_at']);
        }
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
