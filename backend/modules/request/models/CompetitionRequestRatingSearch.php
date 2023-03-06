<?php

namespace backend\modules\request\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CompetitionRequestRatingSearch represents the model behind the search form about `CompetitionRequestRating`.
 */
class CompetitionRequestRatingSearch extends CompetitionRequestRating
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'request_id', 'user_id', 'rating'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
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
        $query = CompetitionRequestRatingSearch::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);


        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'request_id' => $this->request_id,
            'user_id' => $this->user_id,
            'rating' => $this->rating,
        ]);

        return $dataProvider;
    }
}
