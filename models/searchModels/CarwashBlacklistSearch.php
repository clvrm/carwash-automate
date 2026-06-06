<?php

namespace app\models\searchModels;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ar\carwash\CarwashBlacklist;

/**
 * CarwashBlacklistSearch represents the model behind the search form of `app\models\ar\carwash\CarwashBlacklist`.
 */
class CarwashBlacklistSearch extends CarwashBlacklist
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'carwash_id', 'client_id'], 'integer'],
            [['car_number', 'car_region', 'created_at'], 'safe'],
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
        $query = CarwashBlacklist::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);


        $this->load($params);

        if (!empty($params['car_number'])) {
            $this->car_number = $params['car_number'];
        }
        if (!empty($params['car_region'])) {
            $this->car_region = $params['car_region'];
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'carwash_id' => $this->carwash_id,
            'client_id' => $this->client_id,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'car_number', $this->car_number])
            ->andFilterWhere(['like', 'car_region', $this->car_region]);

        return $dataProvider;
    }
}
