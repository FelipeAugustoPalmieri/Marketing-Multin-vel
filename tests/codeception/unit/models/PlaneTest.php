<?php
namespace tests\unit\models;

use app\models\Test;
use Phactory;
use perspectiva\phactory\ActiveRecordTest;
use tests\FakerTrait;
use yii\db\ActiveQuery;

class PlaneTest extends ActiveRecordTest
{
    use FakerTrait;

    public function testValidateProfitValue()
    {
        $sponsorPlane = Phactory::plane();
        $plane = Phactory::plane();
        $this->assertEquals(40.0, $plane->calculateProfitValue($sponsorPlane));
    }

    public function testValidateProfitCustomValue()
    {
        $sponsorPlane = Phactory::plane([
            'bonus_percentage' => 50,
            'value' => 400,
        ]);

        $plane = Phactory::plane([
            'bonus_percentage' => 50,
            'value' => 400,
        ]);

        $this->assertEquals(200.0, $plane->calculateProfitValue($sponsorPlane));
    }

    public function testSponsorOuroConsumerBronze()
    {
        $ouro = Phactory::plane([
            'bonus_percentage' => 40,
            'value' => 600,
        ]);

        $bronze = Phactory::plane([
            'bonus_percentage' => 0,
            'value' => 100,
        ]);

        $this->assertEquals(40.0, $bronze->calculateProfitValue($ouro));
    }

    public function testSponsorBronzeConsumerOuro()
    {
        $ouro = Phactory::plane([
            'bonus_percentage' => 40,
            'value' => 600,
        ]);

        $bronze = Phactory::plane([
            'bonus_percentage' => 0,
            'value' => 100,
        ]);

        $this->assertEquals(0, $ouro->calculateProfitValue($bronze));
    }
}
