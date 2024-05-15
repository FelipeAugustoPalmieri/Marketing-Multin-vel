<?php
namespace tests\unit\models;

use app\models\UserPasswordChangeForm;
use app\models\User;
use Phactory;
use perspectiva\phactory\Test;
use tests\FakerTrait;
use Yii;

class UserPasswordChangeFormTest extends Test
{
    use FakerTrait;

    public function testSaveReturnsFalseWhenInvalid()
    {
        $form = new UserPasswordChangeForm(new User);
        $form->currentPassword = 'INVALID';
        $this->assertFalse($form->save());
    }

    public function testSaveReturnsFalseWithInvalidCurrentPassword()
    {
        $user = Phactory::user('business', ['encrypted_password' => password_hash('old', PASSWORD_BCRYPT)]);
        $form = new UserPasswordChangeForm($user);
        $form->currentPassword = 'invalid';
        $form->newPassword = 'new';
        $form->newPasswordConfirmation = 'new';
        $this->assertFalse($form->save());
    }

    public function testSaveReturnsTrueWithValidData()
    {
        $user = Phactory::user('business', ['encrypted_password' => password_hash('old', PASSWORD_BCRYPT)]);
        $form = new UserPasswordChangeForm($user);
        $form->currentPassword = 'old';
        $form->newPassword = 'new';
        $form->newPasswordConfirmation = 'new';
        $this->assertTrue($form->save());
    }

    public function testSaveChangesUserPassword()
    {
        $oldHash = password_hash('old', PASSWORD_BCRYPT);
        $user = Phactory::user('business', ['encrypted_password' => $oldHash]);
        $form = new UserPasswordChangeForm($user);
        $form->currentPassword = 'old';
        $form->newPassword = 'new';
        $form->newPasswordConfirmation = 'new';

        $form->save();
        $user->refresh();

        $this->assertNotEquals($oldHash, $user->encrypted_password, 'Password should be different.');
    }
}
