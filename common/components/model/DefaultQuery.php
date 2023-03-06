<?php

namespace common\components\model;

use yii\db\ActiveQuery;

/**
 * Class DefaultQuery
 * @package common\models
 *
 * @see ActiveRecord
 */
class DefaultQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function isPublished()
    {
        $this->andWhere(['published' => 1]);
        return $this;
    }

    /**
     * @return $this
     */
    public function deleted()
    {
        $this->andWhere(['deleted' => 1]);
        return $this;
    }

    /**
     * @return $this
     */
    function isNotDeleted()
    {
        $this->andWhere(['deleted' => 0]);
        return $this;
    }
}
