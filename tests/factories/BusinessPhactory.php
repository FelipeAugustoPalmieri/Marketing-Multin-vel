<?php
namespace tests\factories;

use Phactory;
use tests\FakerTrait;

class BusinessPhactory
{
    use FakerTrait;

    public function blueprint()
    {
        return [
            'legalPerson' => Phactory::hasOne('legalPerson', 'physicalPerson'),
            'economic_activity' => 'Marketing',
        ];
    }

    public function juridicalPerson()
    {
        return [
            'legalPerson' => Phactory::hasOne('legalPerson', 'juridicalPerson'),
        ];
    }

    public function physicalPerson()
    {
        return [
            'legalPerson' => Phactory::hasOne('legalPerson', 'physicalPerson'),
        ];
    }
}
