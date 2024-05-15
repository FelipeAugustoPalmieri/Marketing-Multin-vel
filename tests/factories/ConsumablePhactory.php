<?php
namespace tests\factories;

use Phactory;
use tests\FakerTrait;

class ConsumablePhactory
{
    use FakerTrait;

    public function blueprint()
    {
        return [
            'business' => Phactory::business(),
            'description' => $this->faker()->unique()->name,
            'shared_percentage' => 2.5,
        ];
    }
}
