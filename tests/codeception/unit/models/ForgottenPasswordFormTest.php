<?php

namespace tests\codeception\unit\models;

use app\models\ForgottenPasswordForm;
use Yii;
use perspectiva\phactory\Test;
use Phactory;

class ForgottenPasswordFormTest extends Test
{
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

    public function testSaveReturnFalseWhenInvalid()
    {
        $model = new ForgottenPasswordForm;
        $model->email = 'invalid';
        $this->assertFalse($model->save());
    }

    public function testSaveReturnFalseWhenEmailIsNotAssociatedWithAnyUser()
    {
        $model = new ForgottenPasswordForm;
        $model->email = 'invalid.address@example.com';
        $this->assertFalse($model->save());
    }

    public function testSaveReturnTrueWithValidEmail()
    {
        $user = Phactory::user('business');
        $model = new ForgottenPasswordForm;
        $model->email = $user->email;
        $this->assertTrue($model->save());
    }

    public function testSaveGeneratesPasswordResetToken()
    {
        $user = Phactory::user('business');
        $model = new ForgottenPasswordForm;
        $model->email = $user->email;
        $model->save();
        $user->refresh();
        $this->assertNotNull($user->reset_password_token);
    }

    public function testSaveGeneratesSendsPasswordResetInstructions()
    {
        $user = Phactory::user('business');
        $model = new ForgottenPasswordForm;
        $model->email = $user->email;
        $model->save();
        $this->assertTrue(file_exists($this->getMessageFile()));
    }

    protected function getMessageFile()
    {
        return (Yii::$app && Yii::$app->mailer) ? Yii::getAlias(Yii::$app->mailer->fileTransportPath) . '/testing_message.eml' : null;
    }
}
