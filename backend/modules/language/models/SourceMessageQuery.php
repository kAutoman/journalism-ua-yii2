<?php

namespace backend\modules\language\models;

use yii\db\ActiveQuery;
use common\helpers\LanguageHelper;

/**
 * Class SourceMessageQuery
 *
 * @package backend\modules\language\models
 */
class SourceMessageQuery extends ActiveQuery
{
    /**
     * Appends condition for not translated messages
     *
     * @return $this
     */
    public function notTranslated()
    {
        $messageTableName = Message::tableName();
        $query = Message::find()->select($messageTableName . '.id');
        $i = 0;
        foreach (LanguageHelper::getApplicationLanguages() as $language) {
            if ($i === 0) {
                $query->andWhere($messageTableName . '.language = :language and ' . $messageTableName . '.translation is not null and '.$messageTableName. '.translation <> ""', [':language' => $language]);
            } else {
                $query->innerJoin($messageTableName . ' t' . $i, 't' . $i . '.id = ' . $messageTableName . '.id and t' . $i . '.language = :language and t' . $i . '.translation is not null', [':language' => $language]);
            }
            $i++;
        }
        $ids = $query->indexBy('id')->all();
        $this->andWhere(['not in', "$messageTableName.id", array_keys($ids)]);
        return $this;
    }

    /**
     * Appends condition for translated messages
     *
     * @return $this
     */
    public function translated()
    {
        $messageTableName = Message::tableName();
        $query = Message::find()->select($messageTableName . '.id');
        $i = 0;
        foreach (LanguageHelper::getApplicationLanguages() as $language) {
            if ($i === 0) {
                $query->andWhere($messageTableName . '.language = :language and ' . $messageTableName . '.translation is not null and '.$messageTableName. '.translation <> ""', [':language' => $language]);
            } else {
                $query->innerJoin($messageTableName . ' t' . $i, 't' . $i . '.id = ' . $messageTableName . '.id and t' . $i . '.language = :language and t' . $i . '.translation is not null', [':language' => $language]);
            }
            $i++;
        }
        $ids = $query->indexBy('id')->all();
        $this->andWhere(['in', "$messageTableName.id", array_keys($ids)]);
        return $this;
    }
}
