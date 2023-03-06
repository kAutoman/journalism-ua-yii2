<?php

namespace backend\modules\menu\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\menu\models\MenuLang;
use common\behaviors\TranslatedBehavior;

/**
 * MenuSearch represents the model behind the search form about `Menu`.
 *
 * @property string $label
 */
class MenuSearch extends Menu
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'location', 'page_id', 'module', 'position'], 'integer'],
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
        $query = MenuSearch::find()->with(['hasTranslate']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['location' => SORT_ASC, 'position' => SORT_ASC]]
        ]);

        $this->load($params);

        $query->joinWith(['currentTranslate']);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $langTable = MenuLang::tableName();

        $query->andFilterWhere([
            'id' => $this->id,
            'location' => $this->location,
            'page_id' => $this->page_id,
            'module' => $this->module,
            'published' => $this->published,
            'position' => $this->position,
        ]);

        $query->andFilterWhere(['like', 'link', $this->link])
            ->andFilterWhere(['like', "{$langTable}.label", $this->label]);

        return $dataProvider;
    }
}
