<?php
namespace tests\factories;

use Phactory;
use tests\FakerTrait;

class UserPhactory
{
    use FakerTrait;

    public function blueprint()
    {
        return [
            'name' => $this->faker()->unique()->name,
            'login' => $this->faker()->unique()->userName,
            'email' => $this->faker()->unique()->safeEmail,
            'encrypted_password' => password_hash('senha', PASSWORD_BCRYPT),
        ];
    }

    public function business()
    {
        $business = Phactory::business();
        $email = $this->faker()->unique()->safeEmail;
        return [
            'name' => $this->faker()->unique()->name,
            'email' => $email,
            'login' => $email,
            'authenticable_type' => 'Business',
            'authenticable_id' => $business->id,
        ];
    }

    public function consumer()
    {
        $consumer = Phactory::consumer();
        return [
            'name' => $consumer->legalPerson->name,
            'email' => $consumer->legalPerson->email,
            'login' => $consumer->identifier,
            'authenticable_type' => 'Consumer',
            'authenticable_id' => $consumer->id,
        ];
    }
}
