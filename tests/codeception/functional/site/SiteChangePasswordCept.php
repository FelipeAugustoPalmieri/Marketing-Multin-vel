<?php

use perspectiva\phactory\FunctionalTester;
use tests\codeception\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */

$I = new FunctionalTester($scenario);
LoginPage::signInAsAdmin($I);
$user = Phactory::user('business');


// Invalid data
$I->amOnPage('/site/change-password');
$I->fillField('Current Password', 'INVALID');
$I->fillField('New Password', 's3cr3t');
$I->fillField('Repeat New Password', 's3cr3t');
$I->click('Change Password', 'form');
$I->see('Current password is invalid.');

// Valid data
$I->fillField('Current Password', 'admin');
$I->fillField('New Password', 's3cr3t');
$I->fillField('Repeat New Password', 's3cr3t');
$I->click('Change Password', 'form');
$I->see('Password successfully changed.');
