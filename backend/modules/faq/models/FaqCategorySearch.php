<?php

namespace backend\modules\faq\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\faq\models\lang\FaqCategoryLang;
use common\behaviors\TranslatedBehavior;

/**
 * FaqCategorySearch represents the model behind the search form about `FaqCategory`.
 *
 * @property string $label
 */
class FaqCategorySearch extends FaqCategory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'position'], 'integer'],
            [['alias', 'published'], 'safe'],
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
        $query = FaqCategorySearch::find()->with(['hasTranslate']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);

        $query->joinWith(['currentTranslate', 'faqs f']);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $langTable = FaqCategoryLang::tableName();

        $query->andFilterWhere([
            'id' => $this->id,
            'published' => $this->published,
            'position' => $this->position,
        ]);

        $query->andFilterWhere(['like', 'alias', $this->alias])
            ->andFilterWhere(['like', "{$langTable}.label", $this->label]);

        return $dataProvider;
    }
}
