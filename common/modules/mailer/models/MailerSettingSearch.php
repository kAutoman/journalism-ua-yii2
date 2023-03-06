<?php

namespace common\modules\mailer\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * MailerSettingSearch represents the model behind the search form about `Mailer`.
 */
class MailerSettingSearch extends MailerSetting
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'smtp_port'], 'integer'],
            [
                [
                    'label',
                    'subject',
                    'template',
                    'smtp_host',
                    'smtp_encryption',
                    'auth',
                    'smtp_username',
                    'smtp_password',
                    'send_from',
                    'send_to',
                    'send_to_cc',
                    'send_to_bcc',
                    'is_default'
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
        $query = MailerSettingSearch::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);


        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'smtp_port' => $this->smtp_port,
            'auth' => $this->auth,
            'is_default' => $this->is_default
        ]);

        $query->andFilterWhere(['like', 'label', $this->label])
            ->andFilterWhere(['like', 'subject', $this->subject])
            ->andFilterWhere(['like', 'template', $this->template])
            ->andFilterWhere(['like', 'smtp_host', $this->smtp_host])
            ->andFilterWhere(['like', 'smtp_encryption', $this->smtp_encryption])
            ->andFilterWhere(['like', 'smtp_username', $this->smtp_username])
            ->andFilterWhere(['like', 'smtp_password', $this->smtp_password])
            ->andFilterWhere(['like', 'send_from', $this->send_from])
            ->andFilterWhere(['like', 'send_to', $this->send_to])
            ->andFilterWhere(['like', 'send_to_cc', $this->send_to_cc])
            ->andFilterWhere(['like', 'send_to_bcc', $this->send_to_bcc]);

        return $dataProvider;
    }
}
