<?php

namespace backend\modules\member\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\lang\MemberItemLang;
use common\behaviors\TranslatedBehavior;

/**
 * MemberItemSearch represents the model behind the search form about `MemberItem`.
 *
 * @property string $label
 * @property string $content
 */
class MemberItemSearch extends MemberItem
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'position'], 'integer'],
            [['published'], 'safe'],
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
        $query = MemberItemSearch::find()->with(['hasTranslate']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);

        $query->joinWith(['currentTranslate']);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $langTable = MemberItemLang::tableName();

        $query->andFilterWhere([
            'id' => $this->id,
            'published' => $this->published,
            'position' => $this->position,
        ]);

        $query->andFilterWhere(['like', "{$langTable}.label", $this->label])
            ->andFilterWhere(['like', "{$langTable}.content", $this->content]);

        return $dataProvider;
    }
}
