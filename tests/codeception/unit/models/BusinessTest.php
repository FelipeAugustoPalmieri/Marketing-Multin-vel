<?php
namespace tests\unit\models;

use app\models\Business;
use app\models\LegalPerson;
use Phactory;
use perspectiva\phactory\ActiveRecordTest;
use yii\db\ActiveQuery;

class BusinessTest extends ActiveRecordTest
{
    public function testTimestampBehavior()
    {
        $legalPerson = Phactory::legalPerson('physicalPerson');
        $model = Phactory::unsavedBusiness([
            'legalPerson' => $legalPerson,
            'legal_person_id' => $legalPerson->id,
        ]);

        $this->assertNull($model->created_at);
        $this->assertNull($model->updated_at);

        $this->assertTrue($model->save());
        $this->assertNotNull($model->created_at);

        $this->assertTrue($model->save());
        $this->assertNotNull($model->updated_at);
    }

    public function testGetLegalPerson()
    {
        $legalPerson = Phactory::legalPerson('physicalPerson');
        $business = Phactory::unsavedBusiness([
            'legalPerson' => $legalPerson,
            'legal_person_id' => $legalPerson->id,
        ]);
        $this->assertInstanceOf(ActiveQuery::className(), $business->getLegalPerson());
        $this->assertInstanceOf(LegalPerson::className(), $business->legalPerson);
        $this->assertEquals($legalPerson->id, $business->legalPerson->id);
    }

    public function testGetTypeDelegatesToLegalPerson()
    {
        $legalPerson = Phactory::legalPerson('physicalPerson');
        $business = Phactory::unsavedBusiness([
            'legalPerson' => $legalPerson,
            'legal_person_id' => $legalPerson->id,
        ]);
        $this->assertEquals($legalPerson->getType(), $business->getType());
    }

    public function testGetNameDelegatesToLegalPerson()
    {
        $legalPerson = Phactory::legalPerson('physicalPerson');
        $business = Phactory::unsavedBusiness([
            'legalPerson' => $legalPerson,
            'legal_person_id' => $legalPerson->id,
        ]);
        $this->assertEquals($legalPerson->getName(), $business->getName());
    }

    public function testGetNationalIdentifierDelegatesToLegalPerson()
    {
        $legalPerson = Phactory::legalPerson('physicalPerson');
        $business = Phactory::unsavedBusiness([
            'legalPerson' => $legalPerson,
            'legal_person_id' => $legalPerson->id,
        ]);
        $this->assertEquals($legalPerson->getNationalIdentifier(), $business->getNationalIdentifier());
    }

    public function testIsPhysicalPersonDelegatesToLegalPerson()
    {
        $legalPerson = Phactory::legalPerson('physicalPerson');
        $business = Phactory::unsavedBusiness([
            'legalPerson' => $legalPerson,
            'legal_person_id' => $legalPerson->id,
        ]);
        $this->assertEquals($legalPerson->isPhysicalPerson(), $business->isPhysicalPerson());
    }

    public function testIsJuridicalPersonDelegatesToLegalPerson()
    {
        $legalPerson = Phactory::legalPerson('juridicalPerson');
        $business = Phactory::unsavedBusiness([
            'legalPerson' => $legalPerson,
            'legal_person_id' => $legalPerson->id,
        ]);
        $this->assertEquals($legalPerson->isJuridicalPerson(), $business->isJuridicalPerson());
    }
}
