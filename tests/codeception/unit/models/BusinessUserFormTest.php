<?php
namespace tests\unit\models;

use app\models\BusinessUserForm;
use app\models\User;
use Phactory;
use perspectiva\phactory\Test;
use tests\FakerTrait;
use Yii;

class BusinessUserFormTest extends Test
{
    use FakerTrait;

    protected function setUp()
    {
        parent::setUp();
        Yii::$app->mailer->fileTransportCallback = function ($mailer, $message) {
            return 'testing_message.eml';
        };
    }

    protected function tearDown()
    {
        if (file_exists($this->getMessageFile())) {
            unlink($this->getMessageFile());
        }
        parent::tearDown();
    }

    public function testSaveValidates()
    {
        $form = $this->prepareNew();
        $form->name = null;
        $this->assertFalse($form->save());
    }

    public function testSaveAlsoDelegatesValidationToUser()
    {
        Phactory::user('business', ['email' => 'repeated@example.com', 'login' => 'repeated@example.com']);
        $form = $this->prepareNew();
        $form->email = 'repeated@example.com';
        $this->assertFalse($form->save());
    }

    public function testSaveCreatesUser()
    {
        $form = $this->prepareNew();
        $form->login = $form->email;
        $this->assertTrue($form->save());
        $user = User::find()->one();
        $this->assertInstanceOf(User::className(), $user);
        $this->assertEquals($form->email, $user->email);
        $this->assertEquals($form->email, $user->login); // Yeap, it's the same as the e-mail
        $this->assertEquals($form->name, $user->name);
        $this->assertEquals($form->business->id, $user->authenticable_id);
        $this->assertEquals('Business', $user->authenticable_type);
    }

    public function testSaveSendsWelcomeEmail()
    {
        $form = $this->prepareNew();
        $form->login = $form->email;
        $this->assertFalse(file_exists($this->getMessageFile()));
        $this->assertTrue($form->save());
        $this->assertTrue(file_exists($this->getMessageFile()));
    }

    public function testSaveAssignPermissions()
    {
        $form = $this->prepareNew();
        $form->login = $form->email;
        $form->canSeeSalesReport = true;
        $this->assertTrue($form->save());
        $user = User::find()->one();
        $this->assertInstanceOf(User::className(), $user);

        $this->assertTrue(Yii::$app->authManager->checkAccess($user->getId(), 'business'));
        $this->assertTrue(Yii::$app->authManager->checkAccess($user->getId(), 'salesReport'));
    }

    public function testDeleteRemoveUser()
    {
        $user = Phactory::user('business');
        $form = $this->prepareExisting($user);
        $this->assertEquals(1, $form->delete());
        $this->assertEquals(0, User::find()->where(['id' => $user->id])->count());
    }

    public function testGetIsNewRecordDelegatesToUser()
    {
        $newForm = $this->prepareNew();
        $existingForm = $this->prepareExisting(Phactory::user('business'));

        $this->assertTrue($newForm->getIsNewRecord());
        $this->assertFalse($existingForm->getIsNewRecord());
    }

    public function testFindOneReturnsObjectForBusinessUsers()
    {
        $user = Phactory::user('business');
        $this->assertInstanceOf(BusinessUserForm::className(), BusinessUserForm::findOne($user->id));
    }

    public function testFindOneReturnsNullForConsumerUsers()
    {
        $plane = Phactory::plane(['multiplier' => 0.6]);
        $consumer = Phactory::consumer(['plane_id' => $plane->id]);
        $user = Phactory::user([
            'login' => 'consumer',
            'authenticable_type' => 'Consumer',
            'authenticable_id' => $consumer->id,
        ]);

        $this->assertNull(BusinessUserForm::findOne($user->id));
    }

    public function testFindOneReturnsNullForInvalidId()
    {
        $this->assertNull(BusinessUserForm::findOne(0));
    }

    protected function prepareNew()
    {
        $form = new BusinessUserForm;
        $form->business = Phactory::business();
        $form->authManager = Yii::$app->authManager;
        $form->scenario = 'insert';

        $form->email = $this->faker()->safeEmail;
        $form->name = $this->faker()->name;
        $form->password = 'password';
        $form->passwordConfirmation = 'password';

        return $form;
    }

    protected function prepareExisting(User $user)
    {
        $form = BusinessUserForm::findOne($user->id);
        $form->scenario = 'update';
        return $form;
    }

    protected function getMessageFile()
    {
        return Yii::getAlias(Yii::$app->mailer->fileTransportPath) . '/testing_message.eml';
    }
}
