<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Offer;

class OfferSearch extends Offer
{
    public $nomeconvenio;

    public $dt_inicial;
    public $dt_final;

    public function rules()
    {
        return [
            [['id', 'convenio_id'], 'integer'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Offer::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'titulo' => $this->titulo
        ]);

        if($this->nomeconvenio){
            $query->innerJoin('businesses convenio', 'convenio.id = offer.convenio_id');
            $query->innerJoin('legal_persons legal_persons', 'legal_persons.id = convenio.legal_person_id');
            $query->leftJoin('juridical_persons juridical_persons', 'juridical_persons.id = legal_persons.person_id AND legal_persons.person_class = \'JuridicalPerson\'');
        }
        
        if($this->nomeconvenio){
            $query->andFilterWhere(['ilike', 'juridical_persons.trading_name', $this->nomeconvenio]);
        }

        if($this->dt_inicial){
            $query->andWhere(
                'dt_inicial between :dt_inicial and :dt_inicial', 
                [':dt_inicial' => Yii::$app->formatter->asDatetime($this->dt_inicial, 'yyyy/MM/dd')]
            );
        }

        if(!Yii::$app->user->can('admin') && Yii::$app->user->can('salesReport')){
            $query->andWhere(['convenio_id' => Yii::$app->user->identity->authenticable_id]);
        }
        
        if($this->dt_final){
            $query->andWhere(
                'dt_final between :dt_final and :dt_final', 
                [':dt_final' => Yii::$app->formatter->asDatetime($this->dt_final, 'yyyy/MM/dd')]
            );
        }
        
        if($this->titulo){
            $query->andFilterWhere(['ilike', 'titulo', $this->titulo]);
        }



        return $dataProvider;
    }
}
