<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Contratos;

class ContratosSearch extends Contratos
{
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['titulo', 'texto'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Contratos::find();

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
            'titulo' => $this->titulo
        ]);
        if($this->texto){
            $query->andFilterWhere(['ilike', 'texto', $this->texto]);
        }

        return $dataProvider;
    }
}
