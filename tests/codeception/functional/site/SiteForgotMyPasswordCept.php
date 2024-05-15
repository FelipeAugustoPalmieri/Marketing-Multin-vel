<?php

use perspectiva\phactory\FunctionalTester;
use tests\codeception\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */
$I = new FunctionalTester($scenario);
$user = Phactory::user('business');

// Request instructions for invalid account
$I->amOnPage('/site/forgot-my-password');
$I->fillField("//input[@placeholder='Email']", 'invalid_email@example.com');
$I->click('Request New Password');
$I->see('There is no account associated with your e-mail address.');

// Request instructions for valid account
$I->amOnPage('/site/forgot-my-password');
$I->fillField("//input[@placeholder='Email']", $user->email);
$I->click('Request New Password');
$I->see('Please follow the instructions we are sending to your email.');
