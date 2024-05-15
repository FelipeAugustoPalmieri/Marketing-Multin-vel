<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Consumer;

/**
 * ConsumerSearch represents the model behind the search form about `app\models\Consumer`.
 */
class ConsumerSearch extends Consumer
{
    /**
     * Searches by name, identifier or national identifier.
     * @var string
     */
    public $wildCard;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $parentName;

    /**
     * @var string
     */
    public $nationalIdentifier;

    /**
     * @var boolean
     */
    public $ableToHaveChildren;

    /**
     * @var boolean
     */
    public $affiliationPaid;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $phoneNumber;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'legal_person_id', 'parent_consumer_id'], 'integer'],
            [
                [
                    'created_at', 'updated_at', 'bank_name', 'bank_agency',
                    'bank_account', 'name', 'parentName', 'nationalIdentifier',
                    'identifier', 'wildCard', 'phoneNumber', 'email',
                ],
                'safe'
            ],
            [
                ['is_business_representative', 'paid_affiliation_fee', 'affiliationPaid', 'ableToHaveChildren'],
                'boolean',
            ],
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
        $query = Consumer::find()->unpaidAffiliationFirst();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'legal_person_id' => $this->legal_person_id,
            'parent_consumer_id' => $this->parent_consumer_id,
            'is_business_representative' => $this->is_business_representative,
            'paid_affiliation_fee' => $this->paid_affiliation_fee,
            'sponsor_consumer_id' => $this->sponsor_consumer_id,
            'plane_id' => $this->plane_id,
            'identifier' => $this->identifier,
        ]);

        $query->andFilterWhere(['like', 'bank_name', $this->bank_name])
            ->andFilterWhere(['like', 'bank_agency', $this->bank_agency])
            ->andFilterWhere(['like', 'bank_account', $this->bank_account])
        ;

        if ($this->name || $this->nationalIdentifier || $this->wildCard || $this->phoneNumber || $this->email) {
            $query->innerJoin('legal_persons p', 'p.id = consumers.legal_person_id');
            $query->leftJoin('physical_persons pf', 'pf.id = p.person_id AND p.person_class = \'PhysicalPerson\'');
        }

        if ($this->parentName) {
            $query->innerJoin('consumers pai', 'pai.id = consumers.parent_consumer_id');
            $query->innerJoin('legal_persons legal_persons_pai', 'legal_persons_pai.id = pai.legal_person_id');
            $query->leftJoin('physical_persons physical_persons_pai', 'physical_persons_pai.id = legal_persons_pai.person_id AND legal_persons_pai.person_class = \'PhysicalPerson\'');
        }

        if ($this->name) {
            $query->andWhere(
                'pf.name ILIKE :name',
                [':name' => '%' . $this->name . '%']
            );
        }

        if ($this->parentName) {
            $query->andWhere(
                'physical_persons_pai.name ILIKE :name_pai',
                [':name_pai' => '%' . $this->parentName . '%']
            );
        }

        if ($this->nationalIdentifier) {
            $query->andWhere(
                "regexp_replace(pf.cpf, '[^0-9]', '', 'g') ILIKE :codigo",
                [':codigo' => '%' . preg_replace('/[^0-9]/', '', $this->nationalIdentifier) . '%']
            );
        }

        if ($this->wildCard) {
            $fields = [
                'pf.name ILIKE :wildcard_name',
            ];
            $params = [
                ':wildcard_name' => '%' . $this->wildCard . '%',
            ];
            $filteredWildCard = preg_replace('/[^0-9]/', '', $this->wildCard);

            if (!empty($filteredWildCard)) {
                $fields[] = "regexp_replace(pf.cpf, '/[^0-9]/', '', 'g') ILIKE :wildcard_cpf";
                $fields[] = 'consumers.identifier = :wildcard_identifier';
                $params[':wildcard_cpf'] = '%' . $filteredWildCard . '%';
                $params[':wildcard_identifier'] = $this->wildCard;
            }

            $query->andWhere(implode(' OR ', $fields), $params);
        }

        if ($this->email) {
            $query->andWhere(
                'p.email ILIKE :email',
                [':email' => '%' . $this->email . '%']
            );
        }

        if ($this->phoneNumber) {
            $query->andWhere(
                'p.cell_number ILIKE :phone',
                [':phone' => '%' . $this->phoneNumber . '%']
            );
        }

        if (null !== $this->ableToHaveChildren) {
            $query->ableToHaveChildren();
        }

        if (null !== $this->affiliationPaid) {
            $query->affiliationPaid();
        }

        return $dataProvider;
    }
}
