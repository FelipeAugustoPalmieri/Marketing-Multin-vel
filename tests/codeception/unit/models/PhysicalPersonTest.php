<?php
namespace tests\unit\models;

use app\models\PhysicalPerson;
use Phactory;
use perspectiva\phactory\ActiveRecordTest;
use tests\FakerTrait;


class PhysicalPersonTest extends ActiveRecordTest
{
    use FakerTrait;

    public function testTimestampBehavior()
    {
        $model = Phactory::unsavedPhysicalPerson();
        $this->assertNull($model->created_at);
        $this->assertNull($model->updated_at);

        $this->assertTrue($model->save());
        $this->assertNotNull($model->created_at);

        $this->assertTrue($model->save());
        $this->assertNotNull($model->updated_at);
    }

    public function testCpfValidation()
    {
        $model = Phactory::unsavedPhysicalPerson(['cpf' => 'INVALIDO']);
        $this->assertFalse($model->validate());

        $model->cpf = $this->faker()->cpf;
        $this->assertTrue($model->validate());
    }

    public function testRequiresPartnerDataIfMarriedOrCohabiting()
    {
        $model = Phactory::unsavedPhysicalPerson('withoutPartner');
        $model->scenario = 'consumer';
        $this->assertTrue($model->validate());

        $model->marital_status = PhysicalPerson::getMarriedMaritalStatusList()[0];
        $this->assertFalse($model->validate());

        $model->partner_name = 'Moié ou Ómi';
        $model->partner_born_on = '1989-03-07';
        $model->partner_phone_number = '(49) 3000-4000';
        $model->partner_cpf = $this->faker()->cpf;
        $model->partner_rg = $this->faker()->rg;
        $model->partner_issuing_body = 'SSP';

        $this->assertTrue($model->validate());
    }

    public function testDoesntAllowMinorAgeMembers()
    {
        $model = Phactory::physicalPerson('withoutPartner');
        $model->born_on = date('Y-m-d', strtotime('today - 16 years'));
        $this->assertFalse($model->save(), 'Não deve permitir cadastrar legalPerson menor de idade');
        $model->born_on = date('Y-m-d', strtotime('today - 18 years - 1 day'));
        $this->assertTrue($model->save(), 'Deve permitir cadastrar legalPerson maior de idade');
    }
}
