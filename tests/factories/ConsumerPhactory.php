<?php
namespace tests\factories;

use app\models\Consumer;
use Phactory;
use tests\FakerTrait;

class ConsumerPhactory
{
    use FakerTrait;

    public function blueprint()
    {
        return [
            'legalPerson' => Phactory::hasOne('legalPerson', 'physicalPerson'),
            'plane' => Phactory::hasOne('plane'),
            'position' => 'left'
        ];
    }

    /*private function position()
    {
        $position = array_rand(['left', 'right']);

        if ($position == 'left') {
                $position = 'left';
            } else {
                $position = 'right';
        }
        return $position;
    }*/
}
