<?php
namespace tests\factories;

use app\models\Occupation;
use app\models\PhysicalPerson;
use tests\FakerTrait;

class PhysicalPersonPhactory
{
    use FakerTrait;

    public function blueprint()
    {
        $dados = [
            'name' => $this->faker()->unique()->name,
            'cpf' => $this->faker()->unique()->cpf,
            'rg' => $this->faker()->unique()->rg,
            'pis' => rand(100,999) . '.' . rand(10000,99999) . '.' . rand(10,99) . '-' . rand(0,9),
            'nationality' => 'Brasileiro',
            'occupation_id' => Occupation::find()->orderBy('RANDOM()')->one()->id,
            'born_on' => $this->faker()->date('Y-m-d', 'now - 18 years'),
            'marital_status' => PhysicalPerson::getMaritalStatusList()[array_rand(PhysicalPerson::getMaritalStatusList())],
        ];
        if (in_array($dados['marital_status'], PhysicalPerson::getMarriedMaritalStatusList())) {
            $dados['partner_name'] = $this->faker()->unique()->name;
            $dados['partner_born_on'] = $this->faker()->date('Y-m-d', 'now - 18 years');
            $dados['partner_phone_number'] = $this->faker()->phoneNumber;
            $dados['partner_cpf'] = $this->faker()->unique()->cpf;
            $dados['partner_rg'] = $this->faker()->unique()->rg;
            $dados['partner_issuing_body'] = 'SSP';
        }
        return $dados;
    }

    public function withoutPartner()
    {
        return [
            'marital_status' => PhysicalPerson::getSingleMaritalStatusList()[array_rand(PhysicalPerson::getSingleMaritalStatusList())],
            'partner_name' => null,
            'partner_born_on' => null,
            'partner_phone_number' => null,
            'partner_cpf' => null,
            'partner_rg' => null,
            'partner_issuing_body' => null,
        ];
    }

    public function withPartner()
    {
        return [
            'marital_status' => PhysicalPerson::getMarriedMaritalStatusList()[array_rand(PhysicalPerson::getMarriedMaritalStatusList())],
            'partner_name' => $this->faker()->unique()->name,
            'partner_born_on' => $this->faker()->date('Y-m-d', 'now - 18 years'),
            'partner_phone_number' => $this->faker()->phoneNumber,
            'partner_cpf' => $this->faker()->unique()->cpf,
            'partner_rg' => $this->faker()->unique()->rg,
            'partner_issuing_body' => 'SSP',
        ];
    }
}
