<?php

namespace backend\modules\home\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\lang\HomeHeaderSliderLang;
use common\behaviors\TranslatedBehavior;

/**
 * HomeHeaderSliderSearch represents the model behind the search form about `HomeHeaderSlider`.
 *
 * @property string $label
 * @property string $content
 * @property string $button_label
 * @property string $button_src
 */
class HomeHeaderSliderSearch extends HomeHeaderSlider
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'position'], 'integer'],
            [['button_form_enable', 'published'], 'safe'],
            [['label', 'content', 'button_label', 'button_src'], 'safe'],
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
        $query = HomeHeaderSliderSearch::find()->with(['hasTranslate']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);

        $query->joinWith(['currentTranslate']);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $langTable = HomeHeaderSliderLang::tableName();

        $query->andFilterWhere([
            'id' => $this->id,
            'button_form_enable' => $this->button_form_enable,
            'published' => $this->published,
            'position' => $this->position,
        ]);

        $query->andFilterWhere(['like', "{$langTable}.label", $this->label])
            ->andFilterWhere(['like', "{$langTable}.content", $this->content])
            ->andFilterWhere(['like', "{$langTable}.button_label", $this->button_label])
            ->andFilterWhere(['like', "{$langTable}.button_src", $this->button_src]);

        return $dataProvider;
    }
}
