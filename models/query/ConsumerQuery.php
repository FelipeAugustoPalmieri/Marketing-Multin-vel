<?php
namespace app\models\query;

use app\models\Consumer;
use yii\db\ActiveQuery;

class ConsumerQuery extends ActiveQuery
{
    /**
     * @return ConsumerQuery
     */
    public function affiliationPaid()
    {
        return $this->andWhere('paid_affiliation_fee IS TRUE');
    }

    /**
     * @return ConsumerQuery
     */
    public function unpaidAffiliation()
    {
        return $this->andWhere('paid_affiliation_fee IS FALSE');
    }

    /**
     * @return ConsumerQuery
     */
    public function createdBefore($date)
    {
        return $this->andWhere('created_at < :date', [':date' => $date]);
    }

    /**
     * @return ConsumerQuery
     */
    public function unpaidAffiliationFirst()
    {
        return $this->orderBy([
          '(CASE WHEN paid_affiliation_fee THEN 1 ELSE 0 END)' => SORT_ASC,
          'identifier' => SORT_ASC,
        ]);
    }

    /**
     * @return ConsumerQuery
     */
    public function recentFirst()
    {
        return $this->orderBy('created_at DESC');
    }

    /**
     * @return ConsumerQuery
     */
    public function leftFirst()
    {
        return $this->orderBy("(CASE position WHEN 'left' THEN 1 ELSE 2 END)");
    }

    /**
     * @return ConsumerQuery
     */
    public function businessRepresentatives()
    {
        return $this->andWhere('is_business_representative IS TRUE');
    }

    /**
     * @return ConsumerQuery
     */
    public function ableToSponsor()
    {
        return $this->affiliationPaid();
    }

    /**
     * @return ConsumerQuery
     */
    public function ableToHaveChildren()
    {
        return $this->affiliationPaid()->andWhere('
        (
            SELECT COUNT(*)
            FROM consumers filhos
            WHERE filhos.parent_consumer_id = consumers.id
        ) < 2');
    }

    /**
     * @param Consumer $consumer pega quem é pai deste consumer ou que pode ter filhos
     * @return ConsumerQuery
     */
    public function ableToHaveNewChildrenOrParentOf(Consumer $consumer)
    {
        if (empty($consumer->parent_consumer_id)) {
            return $this->ableToHaveChildren();
        }
        return $this->affiliationPaid()->andWhere('
        consumers.id = :pai_considerado
        OR (
            SELECT COUNT(*)
            FROM consumers filhos
            WHERE filhos.parent_consumer_id = consumers.id
        ) < 2', [':pai_considerado' => $consumer->parent_consumer_id]);
    }

    /**
     * @return ConsumerQuery
     */
    public function orderedByName()
    {
        $this->innerJoin('legal_persons', 'legal_persons.id = consumers.legal_person_id');
        $this->innerJoin('physical_persons', 'physical_persons.id = legal_persons.person_id');
        return $this->orderBy('physical_persons.name');
    }
}
