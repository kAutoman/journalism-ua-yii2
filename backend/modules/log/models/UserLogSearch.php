<?php

namespace backend\modules\log\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserLogSearch represents the model behind the search form about `UserLog`.
 */
class UserLogSearch extends UserLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id'], 'integer'],
            [['action', 'model_class', 'entity_id', 'content_before', 'user_info', 'content_after'], 'safe'],
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
        $query = UserLogSearch::find();

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
            'user_id' => $this->user_id,
        ]);

        $query->andFilterWhere(['like', 'action', $this->action])
            ->andFilterWhere(['like', 'model_class', $this->model_class])
            ->andFilterWhere(['like', 'entity_id', $this->entity_id])
            ->andFilterWhere(['like', 'content_before', $this->content_before])
            ->andFilterWhere(['like', 'user_info', $this->user_info])
            ->andFilterWhere(['like', 'content_after', $this->content_after]);

        return $dataProvider;
    }
}
