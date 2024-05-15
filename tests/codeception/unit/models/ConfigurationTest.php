<?php
namespace tests\unit\models;

use app\models\Configuration;
use Phactory;
use perspectiva\phactory\ActiveRecordTest;
use tests\FakerTrait;
use yii\db\ActiveQuery;

class ConfigurationTest extends ActiveRecordTest
{
    use FakerTrait;

    public function testValidateValueTypeFloat()
    {
        $model = new Configuration;
        $model->name = 'Config Float';
        $model->type = 'float';

        $model->value = 'asd';
        $this->assertFalse($model->validate());

        $model->value = 3.4;
        $this->assertTrue($model->validate());
    }

    public function testValidateValueTypeInteger()
    {
        $model = new Configuration;
        $model->name = 'Config Integer';
        $model->type = 'integer';

        $model->value = 2.3;
        $this->assertFalse($model->validate());

        $model->value = 3;
        $this->assertTrue($model->validate());
    }
}
