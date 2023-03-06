<?php

namespace backend\modules\home\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\lang\HomePartnerItemLang;
use common\behaviors\TranslatedBehavior;

/**
 * HomePartnerItemSearch represents the model behind the search form about `HomePartnerItem`.
 *
 * @property string $label
 */
class HomePartnerItemSearch extends HomePartnerItem
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'position'], 'integer'],
            [['link', 'published'], 'safe'],
            [['label'], 'safe'],
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
        $query = HomePartnerItemSearch::find()->with(['hasTranslate']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);

        $query->joinWith(['currentTranslate']);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $langTable = HomePartnerItemLang::tableName();

        $query->andFilterWhere([
            'id' => $this->id,
            'published' => $this->published,
            'position' => $this->position,
        ]);

        $query->andFilterWhere(['like', 'link', $this->link])
            ->andFilterWhere(['like', "{$langTable}.label", $this->label]);

        return $dataProvider;
    }
}
