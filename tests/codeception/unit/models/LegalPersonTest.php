<?php
namespace tests\unit\models;

use app\models\City;
use app\models\LegalPerson;
use Phactory;
use perspectiva\phactory\ActiveRecordTest;
use yii\db\ActiveQuery;

class LegalPersonTest extends ActiveRecordTest
{
    public function testTimestampBehavior()
    {
        $model = Phactory::unsavedLegalPerson([
            'person_class' => 'PhysicalPerson',
            'person_id' => Phactory::physicalPerson()->id,
        ]);
        $this->assertNull($model->created_at);
        $this->assertNull($model->updated_at);

        $this->assertTrue($model->save());
        $this->assertNotNull($model->created_at);

        $this->assertTrue($model->save());
        $this->assertNotNull($model->updated_at);
    }

    public function testOnlyAllowsPhysicalOrJuridicalPerson()
    {
        $model = Phactory::unsavedLegalPerson([
            'person_class' => 'INVALIDO',
        ]);
        $this->assertFalse($model->save());
        $this->assertArrayHasKey('person_class', $model->getErrors());

        $model->person_id = Phactory::physicalPerson()->id;
        $model->person_class = 'PhysicalPerson';
        $this->assertTrue($model->save());

        $model->person_id = Phactory::juridicalPerson()->id;
        $model->person_class = 'JuridicalPerson';
        $this->assertTrue($model->save());
    }

    public function testGetPersonWithJuridicalPerson()
    {
        $juridicalPerson = Phactory::juridicalPerson();
        $model = Phactory::legalPerson([
            'person_class' => 'JuridicalPerson',
            'person_id' => $juridicalPerson->id,
        ]);
        $query = $model->getPerson();

        $this->assertInstanceOf(ActiveQuery::className(), $query);
        $this->assertNotNull($query->one());
        $this->assertEquals($juridicalPerson->id, $query->one()->id);
        $this->assertEquals($juridicalPerson->className(), $query->one()->className());
    }

    public function testGetPersonWithPhysicalPerson()
    {
        $physicalPerson = Phactory::physicalPerson();
        $model = Phactory::legalPerson([
            'person_class' => 'PhysicalPerson',
            'person_id' => $physicalPerson->id,
        ]);
        $query = $model->getPerson();

        $this->assertInstanceOf(ActiveQuery::className(), $query);
        $this->assertNotNull($query->one());
        $this->assertEquals($physicalPerson->id, $query->one()->id);
        $this->assertEquals($physicalPerson->className(), $query->one()->className());
    }

    public function testGetPersonWithInvalidLegalPerson()
    {
        $model = Phactory::unsavedLegalPerson([
            'person_class' => 'INVALIDO',
            'person_id' => 123,
        ]);
        $this->assertNull($model->getPerson());
    }

    public function testGetTypes()
    {
        $this->assertInternalType('array', LegalPerson::getTypes());
    }

    public function testGetType()
    {
        $this->assertEquals(LegalPerson::getTypes()['JuridicalPerson'], Phactory::unsavedLegalPerson(['person_class' => 'JuridicalPerson'])->getType());
        $this->assertEquals(LegalPerson::getTypes()['PhysicalPerson'], Phactory::unsavedLegalPerson(['person_class' => 'PhysicalPerson'])->getType());
        $this->assertNull(Phactory::unsavedLegalPerson(['person_class' => 'INVALIDO'])->getType());
    }

    public function testGetNameWithJuridicalPersonReturnsTradingName()
    {
        $model = Phactory::legalPerson('juridicalPerson');
        $this->assertEquals($model->getPerson()->one()->trading_name, $model->getName());
    }

    public function testGetNameWithPhysicalPersonReturnsName()
    {
        $model = Phactory::legalPerson('physicalPerson');
        $this->assertEquals($model->getPerson()->one()->name, $model->getName());
    }

    public function testGetNameWithLegalInvalidPerson()
    {
        $model = Phactory::unsavedLegalPerson([
            'person_class' => 'INVALIDO',
            'person_id' => 123,
        ]);
        $this->assertNull($model->getName());
    }

    public function testGetNationalIdentifierWithJuridicalPersonReturnsCNPJ()
    {
        $model = Phactory::legalPerson('juridicalPerson');
        $this->assertEquals($model->getPerson()->one()->cnpj, $model->getNationalIdentifier());
    }

    public function testGetNationalIdentifierWithPhysicalPersonReturnsCPF()
    {
        $model = Phactory::legalPerson('physicalPerson');
        $this->assertEquals($model->getPerson()->one()->cpf, $model->getNationalIdentifier());
    }

    public function testGetNationalIdentifierWithInvalidLegalPerson()
    {
        $model = Phactory::unsavedLegalPerson([
            'person_class' => 'INVALIDO',
            'person_id' => 123,
        ]);
        $this->assertNull($model->getNationalIdentifier());
    }

    public function testGetCity()
    {
        $city = City::find()->orderBy('RANDOM()')->one();
        $legalPerson = Phactory::legalPerson('physicalPerson', ['city_id' => $city->id]);
        $this->assertInstanceOf(ActiveQuery::className(), $legalPerson->getCity());
        $this->assertInstanceOf(City::className(), $legalPerson->city);
        $this->assertEquals($city->id, $legalPerson->city->id);
    }

    public function testIsPhysicalPerson()
    {
        $this->assertTrue(Phactory::legalPerson('physicalPerson')->isPhysicalPerson());
        $this->assertFalse(Phactory::legalPerson('juridicalPerson')->isPhysicalPerson());
        $this->assertFalse(Phactory::unsavedLegalPerson(['person_class' => 'INVALIDO'])->isPhysicalPerson());
    }

    public function testIsJuridicalPerson()
    {
        $this->assertTrue(Phactory::legalPerson('juridicalPerson')->isJuridicalPerson());
        $this->assertFalse(Phactory::legalPerson('physicalPerson')->isJuridicalPerson());
        $this->assertFalse(Phactory::unsavedLegalPerson(['person_class' => 'INVALIDO'])->isJuridicalPerson());
    }
}
