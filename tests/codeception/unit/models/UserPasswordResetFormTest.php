<?php
namespace tests\unit\models;

use app\models\UserPasswordResetForm;
use app\models\User;
use Phactory;
use perspectiva\phactory\Test;
use tests\FakerTrait;
use Yii;

class UserPasswordResetFormTest extends Test
{
    use FakerTrait;

    public function testSaveReturnsFalseWhenInvalid()
    {
        $form = new UserPasswordResetForm('invalid_token');
        $form->newPassword = 'password';
        $form->newPasswordConfirmation = 'password';
        $this->assertFalse($form->save());
    }

    public function testSaveReturnsTrueWithValidData()
    {
        $user = Phactory::user('business', ['reset_password_token' => 'token']);
        $form = new UserPasswordResetForm('token');
        $form->newPassword = 'new';
        $form->newPasswordConfirmation = 'new';
        $this->assertTrue($form->save());
    }

    public function testSaveChangesUserPassword()
    {
        $user = Phactory::user('business', ['reset_password_token' => 'token']);
        $oldHash = $user->encrypted_password;
        $form = new UserPasswordResetForm('token');
        $form->newPassword = 'new';
        $form->newPasswordConfirmation = 'new';

        $form->save();
        $user->refresh();

        $this->assertNotEquals($oldHash, $user->encrypted_password, 'Password should be different.');
    }

    public function testSaveDisablePasswordResetToken()
    {
        $user = Phactory::user('business', ['reset_password_token' => 'token']);
        $form = new UserPasswordResetForm('token');
        $form->newPassword = 'new';
        $form->newPasswordConfirmation = 'new';

        $form->save();
        $user->refresh();

        $this->assertNull($user->reset_password_token);
    }
}
