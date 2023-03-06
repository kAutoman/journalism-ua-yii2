<?php

namespace common\models;

use common\components\model\ActiveRecord;

/**
 * This is the model class for table "{{%news_to_category}}".
 *
 * @property integer $id
 * @property integer $category_id
 * @property integer $news_id
 *
 * @property CategoryNews $category
 * @property News $news
 */
class NewsToCategory extends ActiveRecord{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%news_to_category}}';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(CategoryNews::class, ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNews()
    {
        return $this->hasOne(News::class, ['id' => 'news_id']);
    }
}
