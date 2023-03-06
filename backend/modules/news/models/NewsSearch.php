<?php

namespace backend\modules\news\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\lang\NewsLang;
use common\behaviors\TranslatedBehavior;

/**
 * NewsSearch represents the model behind the search form about `News`.
 *
 * @property string $label
 * @property string $content
 */
class NewsSearch extends News
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'position', 'publication_date'], 'integer'],
            [['alias', 'published'], 'safe'],
            [['label', 'content'], 'safe'],
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
        $query = NewsSearch::find()->with(['hasTranslate']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);

        $query->joinWith(['currentTranslate']);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $langTable = NewsLang::tableName();

        $query->andFilterWhere([
            'id' => $this->id,
            'published' => $this->published,
            'position' => $this->position,
            'publication_date' => $this->publication_date,
        ]);

        $query->andFilterWhere(['like', 'alias', $this->alias])
            ->andFilterWhere(['like', "{$langTable}.label", $this->label])
            ->andFilterWhere(['like', "{$langTable}.content", $this->content]);

        return $dataProvider;
    }
}
