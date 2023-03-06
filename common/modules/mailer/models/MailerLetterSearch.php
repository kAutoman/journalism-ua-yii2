<?php

namespace common\modules\mailer\models;

use Cake\Chronos\Chronos;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * MailerLetterSearch represents the model behind the search form about `MailerLetter`.
 */
class MailerLetterSearch extends MailerLetter
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['connection_id', 'date_create', 'date_update', 'subject', 'body', 'recipients', 'attachments'], 'safe'],
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
        $query = MailerLetterSearch::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['date_create'=>SORT_DESC]]
        ]);

        $this->load($params);


        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
        ]);

        if ($this->date_create) {
            $date = new Chronos($this->date_create);
            $startDate = $date->startOfDay()->getTimestamp();
            $endDate = $date->endOfDay()->getTimestamp();
            $query->andFilterWhere(['between', 'date_create', $startDate, $endDate]);
        }

        if ($this->date_update) {
            $date = new Chronos($this->date_update);
            $startDate = $date->startOfDay()->getTimestamp();
            $endDate = $date->endOfDay()->getTimestamp();
            $query->andFilterWhere(['between', 'date_update', $startDate, $endDate]);
        }

        $query->andFilterWhere(['like', 'connection_id', $this->connection_id])
            ->andFilterWhere(['like', 'subject', $this->subject])
            ->andFilterWhere(['like', 'body', $this->body])
            ->andFilterWhere(['like', 'recipients', $this->recipients])
            ->andFilterWhere(['like', 'attachments', $this->attachments]);

        return $dataProvider;
    }
}
