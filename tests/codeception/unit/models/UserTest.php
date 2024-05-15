<?php
namespace tests\unit\models;

use app\models\User;
use Phactory;
use perspectiva\phactory\ActiveRecordTest;

class UserTest extends ActiveRecordTest
{
    public function testTimestampBehavior()
    {
        $model = Phactory::unsavedUser('business');
        $this->assertNull($model->created_at);
        $this->assertNull($model->updated_at);

        $model->save();
        $this->assertNotNull($model->created_at);

        $model->save();
        $this->assertNotNull($model->updated_at);
    }

    public function testAuthenticableMustBeAValidBusinessOrConsumer()
    {
        $model = Phactory::unsavedUser([
            'authenticable_type' => 'Invalid',
            'authenticable_id' => 0,
        ]);
        $this->assertFalse($model->save());

        $model->authenticable_type = 'Business';
        $model->authenticable_id = Phactory::business()->id;
        $this->assertTrue($model->save());

        $model->authenticable_type = 'Consumer';
        $model->authenticable_id = Phactory::consumer()->id;
        $this->assertTrue($model->save());
    }

    public function testFindIdentitySearchesByID()
    {
        $model = Phactory::user('business');
        $result = User::findIdentity($model->id);
        $this->assertInstanceOf(User::className(), $result);
        $this->assertEquals($model->attributes, $result->attributes);
    }

    public function testGetIdReturnsIDValue()
    {
        $model = Phactory::user('business');
        $this->assertEquals($model->id, $model->getId());
    }

    public function testGetAuthKeyRetornusEncryptedPasswordValue()
    {
        $model = Phactory::user('business');
        $this->assertEquals($model->encrypted_password, $model->getAuthKey());
    }

    public function testValidateAuthKeyChecksIfBothPasswordHashesMatch()
    {
        $model = Phactory::user('business');
        $this->assertTrue($model->validateAuthKey($model->encrypted_password));
        $this->assertFalse($model->validateAuthKey('outro_hash'));
    }

    public function testFindIdentityByAccessTokenThrowsException()
    {
        $this->setExpectedException('BadMethodCallException');

        User::findIdentityByAccessToken('foo');
    }
}
