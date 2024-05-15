<?php

use perspectiva\phactory\FunctionalTester;
use tests\codeception\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */
$I = new FunctionalTester($scenario);
$user = Phactory::user('business', ['reset_password_token' => 'secret_token']);

$I->amOnPage(['site/reset-password', 'token' => 'secret_token']);
$I->fillField('New Password', '1234');
$I->fillField('Repeat New Password', '1234');
$I->click('Change Password', 'form');
$I->see('Now you can sign in with your account ' . $user->login . ' and your new password.');
