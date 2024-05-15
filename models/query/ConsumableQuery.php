<?php
namespace app\models\query;

use app\models\Consumable;
use yii\db\ActiveQuery;

class ConsumableQuery extends ActiveQuery
{
   public function ofBusiness($id)
   {
        $this->andWhere('business_id = :id', [':id' => $id]);
        return $this;
    }
}
