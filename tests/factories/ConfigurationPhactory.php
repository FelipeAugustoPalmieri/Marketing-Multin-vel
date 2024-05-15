<?php
namespace tests\factories;

use tests\FakerTrait;
use app\models\Configuration;

class ConfigurationPhactory
{
    use FakerTrait;

    public function blueprint()
    {
        $randomType = array_rand(self::getTypes());
        $randomType = self::getTypes()[$randomType];
        $randomValue = null;


        if ($randomType == 'integer') {
            $randomValue = (int) $this->faker()->randomNumber;
        } elseif ($randomType == 'float') {
            $randomValue = $this->faker()->randomFloat;
        } elseif ($randomType == 'string') {
            $randomValue = $this->faker()->word;
        }

        return [
            'name' => $this->faker()->unique()->name,
            'type' => $randomType,
            'value' => $randomValue,
        ];
    }

    protected static function getTypes()
    {
        return array_keys(Configuration::getTypes());
    }
}
