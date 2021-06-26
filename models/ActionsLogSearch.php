<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ActionsLog;

/**
 * ActionsLogSearch represents the model behind the search form of `app\models\ActionsLog`.
 */
class ActionsLogSearch extends ActionsLog
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'uid', 'time', 'app_version', 'client_version'], 'integer'],
            [['action', 'client', 'user_ip', 'params', 'answer'], 'safe'],
            [['execution_time', 'memory_usage'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
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
    public function search($params)
    {
        $query = ActionsLog::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'uid' => $this->uid,
            'time' => $this->time,
            'app_version' => $this->app_version,
            'client_version' => $this->client_version,
            'execution_time' => $this->execution_time,
            'memory_usage' => $this->memory_usage,
        ]);

        $query->andFilterWhere(['like', 'action', $this->action])
            ->andFilterWhere(['like', 'client', $this->client])
            ->andFilterWhere(['like', 'user_ip', $this->user_ip])
            ->andFilterWhere(['like', 'params', $this->params])
            ->andFilterWhere(['like', 'answer', $this->answer]);

        return $dataProvider;
    }
}
