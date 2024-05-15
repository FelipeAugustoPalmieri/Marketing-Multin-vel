<?php
namespace tests\unit\models;

use app\models\City;
use app\models\State;
use Phactory;
use perspectiva\phactory\ActiveRecordTest;
use tests\FakerTrait;
use yii\db\ActiveQuery;

class StateTest extends ActiveRecordTest
{
    use FakerTrait;

    public function testTimestampBehavior()
    {
        $model = new State;
        $model->name = 'Sulão';
        $model->abbreviation = 'SL';
        $this->assertNull($model->created_at);
        $this->assertNull($model->updated_at);

        $this->assertTrue($model->save());
        $this->assertNotNull($model->created_at);

        $this->assertTrue($model->save());
        $this->assertNotNull($model->updated_at);
    }

    public function testGetCities()
    {
        $state = State::find()->one();
        $this->assertInstanceOf(ActiveQuery::className(), $state->getCities());
        $this->assertInternalType('array', $state->cities);
        $this->assertInstanceOf(City::className(), $state->cities[0]);
        $this->assertEquals($state->id, $state->cities[0]->state_id);
    }
}
