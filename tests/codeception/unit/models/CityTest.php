<?php
namespace tests\unit\models;

use app\models\City;
use app\models\State;
use app\models\LegalPerson;
use Phactory;
use perspectiva\phactory\ActiveRecordTest;
use tests\FakerTrait;
use yii\db\ActiveQuery;

class CityTest extends ActiveRecordTest
{
    use FakerTrait;

    public function testTimestampBehavior()
    {
        $model = new City;
        $model->name = 'Nova Chapecó';
        $model->state_id = State::find()->one()->id;
        $this->assertNull($model->created_at);
        $this->assertNull($model->updated_at);

        $this->assertTrue($model->save());
        $this->assertNotNull($model->created_at);

        $this->assertTrue($model->save());
        $this->assertNotNull($model->updated_at);
    }

    public function testGetLegalPersons()
    {
        $city = City::find()->one();
        $legalPerson = Phactory::legalPerson('physicalPerson', ['city' => $city]);
        $this->assertInstanceOf(ActiveQuery::className(), $city->getLegalPersons());
        $this->assertInternalType('array', $city->legalPersons);
        $this->assertInstanceOf(LegalPerson::className(), $city->legalPersons[0]);
        $this->assertEquals($legalPerson->id, $city->legalPersons[0]->id);
    }

    public function testGetState()
    {
        $city = City::find()->one();
        $query = $city->getState();

        $this->assertInstanceOf(ActiveQuery::className(), $query);
        $this->assertInstanceOf(State::className(), $query->one());
        $this->assertEquals($city->state_id, $query->one()->id);
    }
}
