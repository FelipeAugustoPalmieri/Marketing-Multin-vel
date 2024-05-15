<?php
namespace tests\factories;

use Phactory;
use tests\FakerTrait;
use app\models\City;

class SalesRepresentativeCityPhactory
{
    use FakerTrait;

    public function blueprint()
    {
        return [
            'sales_representative_id' => Phactory::hasOne('consumer'),
            'city_id' => City::find()->orderBy('RANDOM()')->one()->id,
        ];
    }
}
