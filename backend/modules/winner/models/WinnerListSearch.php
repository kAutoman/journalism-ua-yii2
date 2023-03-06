<?php

namespace backend\modules\winner\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\lang\WinnerListLang;
use common\behaviors\TranslatedBehavior;

/**
 * WinnerListSearch represents the model behind the search form about `WinnerList`.
 *
 * @property string $name
 * @property string $publication_label
 */
class WinnerListSearch extends WinnerList
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'member_item_id', 'position'], 'integer'],
            [['publication_link', 'published'], 'safe'],
            [['name', 'publication_label'], 'safe'],
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
        $query = WinnerListSearch::find()->with(['hasTranslate']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);

        $query->joinWith(['currentTranslate']);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $langTable = WinnerListLang::tableName();

        $query->andFilterWhere([
            'id' => $this->id,
            'member_item_id' => $this->member_item_id,
            'published' => $this->published,
            'position' => $this->position,
        ]);

        $query->andFilterWhere(['like', 'publication_link', $this->publication_link])
            ->andFilterWhere(['like', "{$langTable}.name", $this->name])
            ->andFilterWhere(['like', "{$langTable}.publication_label", $this->publication_label]);

        return $dataProvider;
    }
}
