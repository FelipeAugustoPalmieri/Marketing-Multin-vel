<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SalesRepresentativeCity;

class SalesRepresentativeCitySearch extends SalesRepresentativeCity
{
    public function rules()
    {
        return [
            [['id', 'sales_representative_id', 'city_id'], 'integer'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = SalesRepresentativeCity::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'sales_representative_id' => $this->sales_representative_id,
            'city_id' => $this->city_id,
        ]);

        return $dataProvider;
    }
}
