<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Business;

/**
 * BusinessSearch represents the model behind the search form about `app\models\Business`.
 */
class BusinessSearch extends Business
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $cell_number;

    /**
     * @var string
     */
    public $legalPersonType;

    /**
     * @var string
     */
    public $nationalIdentifier;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'legal_person_id'], 'integer'],
            [['created_at', 'economic_activity', 'updated_at', 'name', 'nationalIdentifier', 'legalPersonType', 'cell_number', 'is_disabled'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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
        $query = Business::find();

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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'legal_person_id' => $this->legal_person_id,
            'is_disabled' => $this->is_disabled,

        ]);
        $query->andFilterWhere(['ilike', 'economic_activity', $this->economic_activity]);

        if ($this->name || $this->nationalIdentifier || $this->legalPersonType || $this->cell_number) {
            $query->innerJoin('legal_persons p', 'p.id = businesses.legal_person_id');
            $query->leftJoin('juridical_persons pj', 'pj.id = p.person_id AND p.person_class = \'JuridicalPerson\'');
            $query->leftJoin('physical_persons pf', 'pf.id = p.person_id AND p.person_class = \'PhysicalPerson\'');
        }

        if ($this->legalPersonType) {
            $query->andWhere(
                'p.person_class = :person_legalPerson',
                [':person_legalPerson' => $this->legalPersonType]
            );
        }

        if ($this->name) {
            $query->andWhere(
                'pj.trading_name ILIKE :name OR pf.name ILIKE :name',
                [':name' => '%' . $this->name . '%']
            );
        }

        if ($this->nationalIdentifier) {
            $query->andWhere(
                "regexp_replace(pj.cnpj, '/[^0-9]/', '', 'g') ILIKE :codigo
                OR regexp_replace(pf.cpf, '/[^0-9]/', '', 'g') ILIKE :codigo",
                [':codigo' => '%' . preg_replace('/[^0-9]/', '', $this->nationalIdentifier) . '%']
            );
        }

        if ($this->cell_number) {
            $query->andWhere(
                "regexp_replace(p.cell_number, '/[^0-9]/', '', 'g') ILIKE :phone",
                [':phone' => '%' . preg_replace('/[^0-9]/', '', $this->cell_number) . '%']
            );
        }

        return $dataProvider;
    }
}
