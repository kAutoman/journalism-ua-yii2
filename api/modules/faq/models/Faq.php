<?php

namespace api\modules\faq\models;


use common\behaviors\TranslatedBehavior;
use common\modules\faq\models\Faq as CommonFaq;
use yii\data\ActiveDataProvider;

/**
 * Class Faq
 *
 * @property integer $category
 *
 * @package api\modules\faq\models
 */
class Faq extends CommonFaq
{

    public $category;

    public function fields()
    {
        return [
            'question',
            'answer'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'category'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        // clear all behaviours except translations
        return [
            'translated' => [
                'class' => TranslatedBehavior::class,
                'translateAttributes' => $this->getLangAttributes()
            ],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function getDataProvider($params)
    {
        $query = self::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['position' => SORT_ASC]]
        ]);

        $this->load($params, '');

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['category' => $this->category_id]);

        return $dataProvider;
    }


}
