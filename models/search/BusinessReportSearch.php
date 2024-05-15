<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Business;

class BusinessReportSearch extends Business
{
    public $name;
    public $cell_number;
    public $legalPersonType;
    public $nationalIdentifier;
    public $address;
    public $city;

    public function rules()
    {
        return [
            [['id', 'legal_person_id'], 'integer'],
            [['created_at', 'economic_activity', 'updated_at', 'name', 'nationalIdentifier', 'legalPersonType', 'cell_number', 'address', 'city'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Business::find()->where("is_disabled = FALSE");

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'legal_person_id' => $this->legal_person_id,
        ]);
        $query->andFilterWhere(['ilike', 'economic_activity', $this->economic_activity]);
        $query->andFilterWhere(['ilike', 'address', $this->address]);

        $query->innerJoin('legal_persons p', 'p.id = businesses.legal_person_id');
        $query->leftJoin('juridical_persons pj', 'pj.id = p.person_id AND p.person_class = \'JuridicalPerson\'');
        $query->leftJoin('physical_persons pf', 'pf.id = p.person_id AND p.person_class = \'PhysicalPerson\'');

        if ($this->name) {
            $query->andWhere(
                'pj.trading_name ILIKE :name OR pf.name ILIKE :name',
                [':name' => '%' . $this->name . '%']
            );
        }

        if ($this->cell_number) {
            $query->andWhere(
                "regexp_replace(p.cell_number, '/[^0-9]/', '', 'g') ILIKE :phone",
                [':phone' => '%' . preg_replace('/[^0-9]/', '', $this->cell_number) . '%']
            );
        }

        if ($this->city) {
            $query->andWhere(
                'p.city_id = :cityId',
                [':cityId' => $this->city]
            );
        }

        $query->orderBy(['COALESCE(pj.trading_name, pf.name)' => SORT_ASC]);

        return $dataProvider;
    }
}