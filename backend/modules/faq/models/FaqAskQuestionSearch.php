<?php

namespace backend\modules\faq\models;

use Cake\Chronos\Chronos;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * FaqAskQuestionSearch represents the model behind the search form about `FaqAskQuestion`.
 */
class FaqAskQuestionSearch extends FaqAskQuestion
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name', 'question', 'created_at'], 'safe'],
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
        $query = FaqAskQuestionSearch::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['created_at' => SORT_DESC]]
        ]);

        $this->load($params);


        if (!$this->validate()) {
            return $dataProvider;
        }

        if ($this->created_at) {
            $date = new Chronos($this->created_at);
            $startDate = $date->startOfDay()->getTimestamp();
            $endDate = $date->endOfDay()->getTimestamp();
            $query->andFilterWhere(['between', 'created_at', $startDate, $endDate]);
        }

        $query->andFilterWhere([
                'or',
                ['like', 'name', $this->name],
                ['like', 'phone', $this->name],
                ['like', 'email', $this->name],
            ])
            ->andFilterWhere(['like', 'question', $this->question]);

        return $dataProvider;
    }
}
