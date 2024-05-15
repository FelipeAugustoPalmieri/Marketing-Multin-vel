<?php
namespace tests\factories;

use tests\FakerTrait;

class JuridicalPersonPhactory
{
    use FakerTrait;

    public function blueprint()
    {
        return [
            'company_name' => $this->faker()->unique()->company,
            'trading_name' => $this->faker()->unique()->company,
            'contact_name' => $this->faker()->unique()->name,
            'cnpj' => $this->faker()->unique()->cnpj,
            'ie' => null,
        ];
    }
}
