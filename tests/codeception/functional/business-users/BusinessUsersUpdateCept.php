<?php

use perspectiva\phactory\FunctionalTester;
use tests\codeception\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */
$I = new FunctionalTester($scenario);
$business = Phactory::business('physicalPerson');
$user = Phactory::user([
    'authenticable_type' => 'Business',
    'authenticable_id' => $business->id
]);
LoginPage::signInAsAdmin($I);

$I->amOnPage('/business-users/update?id=' . $user->id);
$I->fillField('Name', 'Another Name');
$I->click('Update');
$I->see('User successfully updated.');
$I->see('Another Name');

// Invalid data
$I->amOnPage('/business-users/update?id=' . $user->id);
$I->fillField('Name', '');
$I->click('Update');
$I->dontSee('User successfully updated.');
