<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Plane;

class PlaneSearch extends Plane
{
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name_plane'], 'safe'],
            [['multiplier', 'goal_points', 'value', 'bonus_percentage'], 'number'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Plane::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'multiplier' => $this->multiplier,
            'goal_points' => $this->goal_points,
            'value' => $this->value,
            'bonus_percentage' => $this->bonus_percentage,
        ]);

        $query->andFilterWhere(['ilike', 'name_plane', $this->name_plane]);

        return $dataProvider;
    }
}
