<?php
namespace tests\factories;

use Phactory;
use tests\FakerTrait;

class PlanePhactory
{
    use FakerTrait;

    public function blueprint()
    {
        return [
            'name_plane' => $this->faker()->unique()->name,
            'multiplier' => 2.5,
            'goal_points' => 3.5,
            'bonus_percentage' => 10,
            'value' => 400,
        ];
    }
}
