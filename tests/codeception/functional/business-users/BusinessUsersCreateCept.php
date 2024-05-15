<?php

use app\models\City;
use perspectiva\phactory\FunctionalTester;
use tests\codeception\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */

$I = new FunctionalTester($scenario);
LoginPage::signInAsAdmin($I);
$business = Phactory::business('physicalPerson');
$user = Phactory::unsavedUser();



$I->amOnPage('/business-users/create?businessId=' . $business->id);
$I->fillField('Name', $user->name);
$I->fillField('Email', $user->email);
$I->fillField('Login', $user->login);
$I->fillField('Password', '123456');
$I->fillField('Password Confirmation', '123456');
$I->click('Create');
$I->see('User successfully created.');
$I->see($user->name);
$I->see($user->email);

// Invalid data
$I->amOnPage('/business-users/create?businessId=' . $business->id);
$I->fillField('Name', '');
$I->click('Create');
$I->dontSee('User successfully created.');
