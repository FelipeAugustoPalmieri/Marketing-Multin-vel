<?php

use perspectiva\phactory\FunctionalTester;
use tests\codeception\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */
$I = new FunctionalTester($scenario);
$business = Phactory::business('physicalPerson');
$consumable = Phactory::consumable([
    'business' => $business,
]);
LoginPage::signInAsAdmin($I);

$I->amOnPage('/consumable/update?id=' . $consumable->id . '&=businessId= ' . $business->id);
$I->fillField('Description', 'Another Description');
$I->click('Update');
$I->see('Consumable successfully updated.');
$I->see('Another Description');

// Invalid data
$I->amOnPage('/consumable/update?id=' . $consumable->id . '&businessId= ' . $business->id);;
$I->fillField('Description', '');
$I->click('Update');
$I->dontSee('Consumable successfully updated.');
