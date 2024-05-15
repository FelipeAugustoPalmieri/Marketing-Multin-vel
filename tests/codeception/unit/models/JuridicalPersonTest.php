<?php
namespace tests\unit\models;

use Phactory;
use perspectiva\phactory\ActiveRecordTest;
use tests\FakerTrait;

class JuridicalPersonTest extends ActiveRecordTest
{
    use FakerTrait;

    public function testTimestampBehavior()
    {
        $model = Phactory::unsavedJuridicalPerson();
        $this->assertNull($model->created_at);
        $this->assertNull($model->updated_at);

        $this->assertTrue($model->save());
        $this->assertNotNull($model->created_at);

        $this->assertTrue($model->save());
        $this->assertNotNull($model->updated_at);
    }

    public function testCnpjValidation()
    {
        $model = Phactory::unsavedJuridicalPerson(['cnpj' => 'INVALIDO']);
        $this->assertFalse($model->save());

        $model->cnpj = $this->faker()->cnpj;
        $this->assertTrue($model->save());
    }
}
