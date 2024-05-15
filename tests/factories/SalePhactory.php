<?php
namespace tests\factories;

use Phactory;
use tests\FakerTrait;

class SalePhactory
{
    use FakerTrait;

    public function blueprint()
    {
        return [
            'consumer' => Phactory::hasOne('consumer'),
            'business' => Phactory::hasOne('business'),
            'consumable' => Phactory::hasOne('consumable'),
            'invoice_code' => (string) rand(150,25000),
            'total' => rand(150,250),
        ];
    }
}
