<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "fpm_file".
 *
 * @property integer $id
 * @property string $extension
 * @property string $base_name
 * @property integer $created_at
 */
abstract class FpmFile extends \common\components\model\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fpm_file';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return \yii\helpers\ArrayHelper::merge(parent::behaviors(), [
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['extension'], 'required'],
            [['extension'], 'string', 'max' => 10],
            [['base_name'], 'string', 'max' => 250],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'extension' => 'Extension',
            'base_name' => 'Base Name',
        ];
    }
}
