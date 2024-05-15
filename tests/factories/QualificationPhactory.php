<?php
namespace tests\factories;

use tests\FakerTrait;
use app\models\Configuration;

class QualificationPhactory
{
    use FakerTrait;

    public function blueprint()
    {
        return [
            'description' => $this->faker()->unique()->name,
            'gain_percentage' => 2.5,
            'position' => rand(1,100),
        ];
    }
}
