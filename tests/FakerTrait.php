<?php
namespace tests;

use Faker\Factory;
use Faker\Provider\pt_BR\Address;
use Faker\Provider\pt_BR\PhoneNumber;
use Faker\Provider\pt_BR\Person;
use Faker\Provider\pt_BR\Company;

trait FakerTrait
{
    protected static $faker;

    protected function faker()
    {
        if (null === self::$faker) {
            self::$faker = Factory::create();
            self::$faker->addProvider(new Address(self::$faker));
            self::$faker->addProvider(new PhoneNumber(self::$faker));
            self::$faker->addProvider(new Person(self::$faker));
            self::$faker->addProvider(new Company(self::$faker));
        }
        return self::$faker;
    }
}
