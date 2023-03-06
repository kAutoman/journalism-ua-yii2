<?php

namespace backend\modules\home\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\lang\HomeCouncilItemLang;
use common\behaviors\TranslatedBehavior;

/**
 * HomeCouncilItemSearch represents the model behind the search form about `HomeCouncilItem`.
 *
 * @property string $label
 * @property string $description
 */
class HomeCouncilItemSearch extends HomeCouncilItem
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'position'], 'integer'],
            [['published'], 'safe'],
            [['label', 'description'], 'safe'],
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
        $query = HomeCouncilItemSearch::find()->with(['hasTranslate']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);

        $query->joinWith(['currentTranslate']);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $langTable = HomeCouncilItemLang::tableName();

        $query->andFilterWhere([
            'id' => $this->id,
            'published' => $this->published,
            'position' => $this->position,
        ]);

        $query->andFilterWhere(['like', "{$langTable}.label", $this->label])
            ->andFilterWhere(['like', "{$langTable}.description", $this->description]);

        return $dataProvider;
    }
}
