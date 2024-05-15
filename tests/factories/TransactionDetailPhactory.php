<?php
namespace tests\factories;

use Phactory;
use tests\FakerTrait;

class TransactionDetailPhactory
{
    use FakerTrait;

    public function blueprint()
    {
        $sale = Phactory::sale();

        return [
            'object_type' => 'Sale',
            'object_id' => $sale->id,
            'consumer_id' => $sale->consumer->id,
            'plane_id' => $sale->consumer->plane->id,
            'profit_percentage' => 10,
            'profit' => 40,
            'created_at' => date('Y-m-d H:i:s'),
        ];
    }
}
