<?php
namespace tests\factories;

use app\models\City;
use Phactory;
use tests\FakerTrait;

class LegalPersonPhactory
{
    use FakerTrait;

    public function blueprint()
    {
        return [
            'person_class' => 'PhysicalPerson',
            'person_id' => Phactory::physicalPerson()->id,
            'address' => $this->faker()->streetAddress,
            'district' => $this->faker()->city,
            'city_id' => City::find()->orderBy('RANDOM()')->one()->id,
            'zip_code' => $this->faker()->postcode,
            'cell_number' => $this->faker()->phoneNumber,
            'email' => $this->faker()->safeEmail,
            'website' => rand(0, 2) ? 'http://' . $this->faker()->domainName : null,
        ];
    }

    public function juridicalPerson()
    {
        return [
            'person_class' => 'JuridicalPerson',
            'person_id' => Phactory::juridicalPerson()->id,
        ];
    }

    public function physicalPerson()
    {
        return [
            'person_class' => 'PhysicalPerson',
            'person_id' => Phactory::physicalPerson()->id,
        ];
    }
}
