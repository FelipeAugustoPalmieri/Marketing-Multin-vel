<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PorcentagemInvestimento;

/**
 * ConfirgurationSearch represents the model behind the search form about `app\models\Configuration`.
 */
class PorcentagemSearch extends PorcentagemInvestimento
{
 
    public function rules()
    {
        return [
            [['porcentagem', 'plane_investiment_id'], 'number'],
        ];
    }

    public function searchPreview($params){
        $query = PorcentagemInvestimento::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function search($params)
    {
        $query = PorcentagemInvestimento::find();

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
        ]);

        $query->andFilterWhere(['=', 'porcentagem', $this->porcentagem])
            ->andFilterWhere(['=', 'plane_investiment_id', $this->plane_investiment_id])
            ->andFilterWhere(['=', 'data_referencia', $this->data_referencia]);

        return $dataProvider;
    }
}