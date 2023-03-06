<?php

namespace backend\modules\request\models;

use backend\modules\rbac\models\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Class AcceptedCompetitionRequestSearch
 *
 * @package backend\modules\request\models
 */
class AcceptedCompetitionRequestSearch extends AcceptedCompetitionRequest
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'id',
                    'nomination_id',
                    'status',
                ],
                'integer'
            ],
            [
                [
                    'name',
                    'email',
                    'gender',
                    'age',
                    'city',
                    'company_name',
                    'position',
                    'phone',
                    'experience',
                    'other_name',
                    'material_label',
                    'material_type',
                    'program_label',
                    'program_published_date',
                    'program_link',
                    'nomination',
                    'argument',
                    'awards'
                ],
                'safe'
            ],
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
        $query = AcceptedCompetitionRequestSearch::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);


        if (!$this->validate()) {
            return $dataProvider;
        }

        $nominationId = $this->nomination_id;

        if (user()->can(User::ROLE_JURY) || user()->can(User::ROLE_MODERATOR)) {
            /** @var User $user */
            $user = user()->getIdentity();

            $nominations = map($user->memberItems, 'id', 'id');
            $nominations = array_values($nominations);

            if (!$nominationId) {
                $nominationId = $nominations;
            }
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'nomination_id' => $nominationId,
            'status' => self::STATUS_ACCEPT,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'gender', $this->gender])
            ->andFilterWhere(['like', 'age', $this->age])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'company_name', $this->company_name])
            ->andFilterWhere(['like', 'position', $this->position])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'experience', $this->experience])
            ->andFilterWhere(['like', 'other_name', $this->other_name])
            ->andFilterWhere(['like', 'material_label', $this->material_label])
            ->andFilterWhere(['like', 'material_type', $this->material_type])
            ->andFilterWhere(['like', 'program_label', $this->program_label])
            ->andFilterWhere(['like', 'program_published_date', $this->program_published_date])
            ->andFilterWhere(['like', 'program_link', $this->program_link])
            ->andFilterWhere(['like', 'nomination', $this->nomination])
            ->andFilterWhere(['like', 'argument', $this->argument])
            ->andFilterWhere(['like', 'awards', $this->awards]);

        return $dataProvider;
    }
}
