<?php

namespace app\models;

use app\models\Apartment;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ApartmentSearch represents the model behind the search form of `app\models\Apartment`.
 */
class ApartmentSearch extends Apartment
{

    /**
     * {@inheritdoc}
     */
    public function formName()
    {
        return 'search';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'total_floors', 'floor', 'rooms', 'area', 'rent_price'], 'integer'],
            [['city', 'district', 'address', 'residential_complex', 'block'], 'safe'],
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
        $query = Apartment::find();

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
            'total_floors' => $this->total_floors,
            'floor' => $this->floor,
            'rooms' => $this->rooms,
            'area' => $this->area,
            'rent_price' => $this->rent_price,
        ]);

        $query->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'district', $this->district])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'residential_complex', $this->residential_complex])
            ->andFilterWhere(['like', 'block', $this->block]);

        return $dataProvider;
    }
}
