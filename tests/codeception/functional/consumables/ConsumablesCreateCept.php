<?php

use app\models\Consumables;
use perspectiva\phactory\FunctionalTester;
use tests\codeception\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */
$I = new FunctionalTester($scenario);
$business = Phactory::business('physicalPerson');
$consumable = Phactory::unsavedConsumable();

LoginPage::signInAsAdmin($I);

$I->amOnPage('/consumable/create?businessId=' . $business->id);
$I->fillField('Description', $consumable->description);
$I->fillField('Shared Percentage', $consumable->shared_percentage);
$I->click('Create');
$I->see('Consumable successfully created.');
$I->see($consumable->description);
$I->see('2,50%');

// Invalid data
$I->amOnPage('/consumable/create?businessId=' . $business->id);
$I->fillField('Shared Percentage', '');
$I->click('Create');
$I->dontSee('Consumable successfully created.');
