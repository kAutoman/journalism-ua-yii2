<?php

namespace common\components\model;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord as YiiActiveRecord;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;

/**
 * Class ActiveRecord
 *
 * @property ActiveRecord[] $translations
 * @property ActiveRecord[] $editLang
 *
 * @package common\components\model
 */
class ActiveRecord extends BaseActiveRecord
{
    public $relModelIndex = null;
    public $relatedModels = null;

    /**
     * @var string|bool
     */
    public $langModelClass = false;

    /**
     * @inheritdoc
     * @return DefaultQuery
     */
    public static function find()
    {
        return new DefaultQuery(get_called_class());
    }

    /**
     * @return ActiveQuery
     * @throws MethodNotAllowedHttpException
     */
    public function getTranslations(): ActiveQuery
    {
        if ($this->isTranslatable()) {
            return $this->hasMany($this->langModelClass, ['model_id' => 'id']);
        }

        throw new MethodNotAllowedHttpException('Model is not translatable');
    }

    /**
     * @return bool
     */
    public function isTranslatable(): bool
    {
        return !!$this->langModelClass;
    }

    /**
     * Find model with conditions or create a new one if record not exists.
     *
     * @param array $conditions
     * @return ActiveRecord
     */
    public static function findOneOrCreate(array $conditions): self
    {
        $model = static::findOne($conditions);
        if ($model === null) {
            $model = new static();
        }

        return $model;
    }

    /**
     * Finds one record or throws 404 error if record is not exists.
     *
     * @param array $conditions
     * @return ActiveRecord
     * @throws NotFoundHttpException
     */
    public static function findOneOrFail(array $conditions): self
    {
        $model = static::findOne($conditions);
        if ($model === null) {
            throw new NotFoundHttpException();
        }

        return $model;
    }

    /**
     * @param string $from
     * @param string $to
     *
     * @return array
     */
    public static function getListItems(string $from = 'id', string $to = 'label'): array
    {
        return map(self::find()->all(), $from, $to);
    }

    public static function getSelectize()
    {
        $models = self::find()->isPublished()->all();

        $items = [];
        foreach ($models as $model) {
            $items[] = [
                'id' => $model->id,
                'label' => $model->label
            ];
        }

        return $items;
    }
}
