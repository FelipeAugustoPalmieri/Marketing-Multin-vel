<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Qualification;

class QualificationSearch extends Qualification
{
    public function rules()
    {
        return [
            [['id', 'position', 'completed_levels'], 'integer'],
            [['description'], 'safe'],
            [['gain_percentage', 'points'], 'number'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Qualification::find();

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
            'gain_percentage' => $this->gain_percentage,
            'position' => $this->position,
            'completed_levels' => $this->completed_levels,
            'points' => $this->points,
        ]);

        $query->andFilterWhere(['ilike', 'description', $this->description]);

        return $dataProvider;
    }
}
