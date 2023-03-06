<?php

namespace backend\modules\faq\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\faq\models\lang\FaqLang;
use common\behaviors\TranslatedBehavior;

/**
 * FaqSearch represents the model behind the search form about `Faq`.
 *
 * @property string $question
 * @property string $answer
 */
class FaqSearch extends Faq
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'category_id', 'position'], 'integer'],
            [['published'], 'safe'],
            [['question', 'answer'], 'safe'],
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
        $query = FaqSearch::find()->with(['hasTranslate']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);

        $query->joinWith(['currentTranslate']);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $langTable = FaqLang::tableName();

        $query->andFilterWhere([
            'id' => $this->id,
            'category_id' => $this->category_id,
            'published' => $this->published,
            'position' => $this->position,
        ]);

        $query->andFilterWhere(['like', "{$langTable}.question", $this->question])
            ->andFilterWhere(['like', "{$langTable}.answer", $this->answer]);

        return $dataProvider;
    }
}
